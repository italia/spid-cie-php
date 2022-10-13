<?php
    require_once("{{SDKHOME}}/proxy-spid-php.php");
    require_once("{{SDKHOME}}/lib/ResponseHandler.php");
    require_once("{{SDKHOME}}/lib/ResponseHandlerPlain.php");
    require_once("{{SDKHOME}}/lib/ResponseHandlerSign.php");
    require_once("{{SDKHOME}}/lib/ResponseHandlerSignEncrypt.php");
    require_once("{{SDKHOME}}/lib/ResponseHandlerEncryptSign.php");

    use Jose\Component\Core\AlgorithmManager;
    use Jose\Component\Core\JWK;
    use Jose\Component\KeyManagement\JWKFactory;
    use Jose\Component\Signature\Algorithm\RS256;  
    use Jose\Component\Signature\JWSBuilder;
    use Jose\Component\Signature\Serializer\JWSSerializerManager;
    use Jose\Component\Signature\Serializer\CompactSerializer as JWSSerializer;
    use Jose\Component\Signature\JWSVerifier;
    use Jose\Component\Signature\JWSLoader;
    use Jose\Component\Encryption\Algorithm\KeyEncryption\A256KW;
    use Jose\Component\Encryption\Algorithm\ContentEncryption\A256CBCHS512;
    use Jose\Component\Encryption\Compression\CompressionMethodManager;
    use Jose\Component\Encryption\Compression\Deflate;
    use Jose\Component\Encryption\JWEBuilder; 
    use Jose\Component\Encryption\Serializer\JWESerializerManager;
    use Jose\Component\Encryption\Serializer\CompactSerializer as JWESerializer;
    use Jose\Component\Encryption\JWEDecrypter;

    const PROXY_CONFIG_FILE = "{{SDKHOME}}/spid-php-proxy.json";
    const TOKEN_PRIVATE_KEY = "{{SDKHOME}}/cert/spid-sp.pem";
    const TOKEN_PUBLIC_CERT = "{{SDKHOME}}/cert/spid-sp.crt";
    const DEFAULT_SPID_LEVEL = 2;
    const DEFAULT_ATCS_INDEX = 0;
    const DEFAULT_SECRET = "";
    const DEFAULT_TOKEN_EXPIRATION_TIME = 1200;
    const DEBUG = false;

    $proxy_config = file_exists(PROXY_CONFIG_FILE)? json_decode(file_get_contents(PROXY_CONFIG_FILE), true) : array();
    $production = $proxy_config['production'];

    $clients        = $proxy_config['clients'];
    $action         = $_GET['action'];
    $client_id      = $_GET['client_id'];
    $redirect_uri   = $_GET['redirect_uri'];
    $state          = $_GET['state'];
    $idp            = $_GET['idp'];

    switch($action) {

        case "login":

            $service = "service";
            if($idp=="CIE" || $idp=="CIE TEST") $service = "cie";
        
            $spidsdk = new SPID_PHP($production, $service);


            if(!$spidsdk->isIdPAvailable($idp)) {
                http_response_code(404);
                if(DEBUG) echo "idp not found"; 
                die(); 
            }

            if(in_array($client_id, array_keys($clients))) {
                if(in_array($redirect_uri, $clients[$client_id]['redirect_uri'])) {

                    if($spidsdk->isAuthenticated() 
                    && isset($_GET['idp']) 
                    && $spidsdk->isIdP($_GET['idp'])) {

                        // dearray values
                        $data = array();
                        foreach($spidsdk->getAttributes() as $attribute=>$value) {
                            $data[$attribute] = $value[0];
                        }

                        $client_config = $proxy_config['clients'][$client_id];
                        $handlerClass = 'ResponseHandler'.$client_config['handler'];

                        if(!in_array($handlerClass, [
                            'ResponseHandlerPlain', 
                            'ResponseHandlerSign',
                            'ResponseHandlerSignEncrypt',
                            'ResponseHandlerEncryptSign'
                        ])) {
                            if($proxy_config['signProxyResponse']) {
                                if($proxy_config['encryptProxyResponse']) {
                                    $handlerClass = 'ResponseHandlerEncryptSign';
                                } else {
                                    $handlerClass = 'ResponseHandlerSign';
                                }
                            } else {
                                $handlerClass = 'ResponseHandlerPlain';
                            }
                        }

                        $handler = new $handlerClass($proxy_config['spDomain'], $client_config);
                        $handler->set('providerId', $spidsdk->getIdP());
                        $handler->set('providerName', $spidsdk->getIdPKey());
                        $handler->set('responseId', $spidsdk->getResponseID());
                        
                        $handler->sendResponse($redirect_uri, $data, $state);
                        die();
                
                    } else {
                        $spid_level = $clients[$client_id]['level'];
                        $atcs_index = $clients[$client_id]['atcs_index'];
                        if($spid_level==null || !in_array($spid_level, [1,2,3])) $spid_level = DEFAULT_SPID_LEVEL;
                        if($atcs_index==null || !is_numeric($atcs_index)) $atcs_index = DEFAULT_ATCS_INDEX;

                        $spidsdk->login($idp, $spid_level, "", $atcs_index);
                        die();
                    }

                } else {
                    http_response_code(404);
                    if(DEBUG) echo "redirect_uri not found";  
                    die();
                }

            } else {
                http_response_code(404);
                if(DEBUG) echo "client not found";  
                die();
            }

        break;

        case "logout":
            $return = $redirect_uri? $redirect_uri : $clients[$client_id]['redirect_uri'][0];

            $service = "service";
            $spidsdk = new SPID_PHP($production, $service);
            if(!$spidsdk->isAuthenticated()) {
                $service = "cie";
                $spidsdk = new SPID_PHP($production, $service);
            };

            if($spidsdk->isAuthenticated()) {
                /* 
                 * Uncomment to exec local logout instead of IdP logout
                 */
                /*
                $sspSession = \SimpleSAML\Session::getSessionFromRequest();
                $sspSession->doLogout('service');
                header("location: " . $return);
                */
                $spidsdk->logout();
                die();
            } else {
                header("location: " . $return);
                die();
            }

        break;

        case "verify": 
            $token = $_GET['token'];
            $secret = $_GET['secret']?:'';
            if(!$token) http_response_code(400);
            $decrypt = ($_GET['decrypt'] && strtoupper($_GET['decrypt'])=='Y')? true:false; 

            $algorithmManager = new AlgorithmManager([new RS256()]);
            $jwsVerifier = new JWSVerifier($algorithmManager);
            $jwk = JWKFactory::createFromKeyFile(TOKEN_PUBLIC_CERT);
            $serializerManager = new JWSSerializerManager([ new JWSSerializer() ]);
            $jws = $serializerManager->unserialize($token);
            $isVerified = $jwsVerifier->verifyWithKey($jws, $jwk, 0);
            $payload = $jws->getPayload();

            if($isVerified) {
                $payload_obj = json_decode($payload);
                if($decrypt && array_key_exists('data', $payload_obj)) {
                    $token = $payload_obj->data;
                    
                    $keyEncryptionAlgorithmManager = new AlgorithmManager([ new A256KW() ]);
                    $contentEncryptionAlgorithmManager = new AlgorithmManager([ new A256CBCHS512() ]);
                    $compressionMethodManager = new CompressionMethodManager([ new Deflate() ]);
                    $jweDecrypter = new JWEDecrypter($keyEncryptionAlgorithmManager, $contentEncryptionAlgorithmManager, $compressionMethodManager);

                    $jwk = JWKFactory::createFromSecret($secret);

                    $serializerManager = new JWESerializerManager([ new JWESerializer() ]);
                    $jwe = $serializerManager->unserialize($token);
                    $success = $jweDecrypter->decryptUsingKey($jwe, $jwk, 0);

                    if(!$success) {
                        header('Content-Type: application/json; charset=utf-8');
                        http_response_code(422);
                    }
                    
                    $payload = $jwe->getPayload();
                }

                header('Content-Type: application/json; charset=utf-8');
                echo $payload;
                die();
            } else {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(422);
            }

            die();
        break;
    }

    http_response_code(404);
    if(DEBUG) echo "action not valid"; 
    die(); 


?>

