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
    const DEFAULT_CIE_LEVEL = 3;
    const DEFAULT_ATCS_INDEX = null;    // set to null to retrieve it from metadata
    const DEFAULT_EIDAS_ATCS_INDEX = 100;
    const DEFAULT_SECRET = "";
    const DEFAULT_TOKEN_EXPIRATION_TIME = 1200;
    const PROXY_HOME = "/proxy-home.php";
    const DEBUG = false;

    $proxy_config = file_exists(PROXY_CONFIG_FILE)? json_decode(file_get_contents(PROXY_CONFIG_FILE), true) : array();
    $production = $proxy_config['production'];

    $clients        = $proxy_config['clients'];
    $action         = $_GET['action'];
    $client_id      = $_GET['client_id'];
    $redirect_uri   = isset($_GET['redirect_uri'])? $_GET['redirect_uri'] : $clients[$client_id]['redirect_uri'][0];
    $state          = $_GET['state'];
    $idp            = $_GET['idp'];

    $spidcie_level  = $clients[$client_id]['level'];
    if($spidcie_level===null || !in_array($spidcie_level, [1,2,3])) $spidcie_level = $isCIE? DEFAULT_CIE_LEVEL : DEFAULT_SPID_LEVEL;

    $atcs_index     = $clients[$client_id]['atcs_index'];
    if($atcs_index===null || !is_numeric($atcs_index)) $atcs_index = DEFAULT_ATCS_INDEX;
    if($idp=="EIDAS" || $idp=="EIDAS QA") $atcs_index = DEFAULT_EIDAS_ATCS_INDEX;

    switch($action) {

        case "login":

            if(in_array($client_id, array_keys($clients))) {
                if(in_array($redirect_uri, $clients[$client_id]['redirect_uri'])) {

                    $isCIE = ($idp=="CIE" || $idp=="CIE TEST");
                    $service = $isCIE? "cie" : "spid";
                
                    if(isset($clients[$client_id]['service'])) {
                        $service = $clients[$client_id]['service'];
                    }

                    $spidsdk = new SPID_PHP($production, $service);

                    if(!$spidsdk->isIdPAvailable($idp)) {
                        if(PROXY_HOME) {
                            header('Location: ' . PROXY_HOME . 
                                    '?client_id=' . $client_id .
                                    '&level=' . $spidcie_level .
                                    '&redirect_uri=' . $redirect_uri .
                                    '&state=' . $state);
                        } else {
                            http_response_code(404);
                            if(DEBUG) echo "idp not found"; 
                        }

                        die(); 
                    }

                    if($spidsdk->isAuthenticated() 
                    && isset($_GET['idp']) 
                    && $spidsdk->isIdP($_GET['idp'])) {

                        // dearray values
                        $data = array();
                        foreach($spidsdk->getAttributes() as $attribute=>$value) {
                            $response_attributes_prefix = $proxy_config['clients'][$client_id]['response_attributes_prefix'];
                            $response_attributes_prefix = $response_attributes_prefix? $response_attributes_prefix : '';
                            $data[$response_attributes_prefix.$attribute] = $value[0];
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
                        /*
                        $spidcie_level = $clients[$client_id]['level'];
                        $atcs_index = $clients[$client_id]['atcs_index'];
                        if($spidcie_level===null || !in_array($spidcie_level, [1,2,3])) $spidcie_level = $isCIE? DEFAULT_CIE_LEVEL : DEFAULT_SPID_LEVEL;
                        if($atcs_index===null || !is_numeric($atcs_index)) $atcs_index = DEFAULT_ATCS_INDEX;

                        if($idp=="EIDAS" || $idp=="EIDAS QA") $atcs_index = DEFAULT_EIDAS_ATCS_INDEX;
                        */

                        $returnTo = $_SERVER['SCRIPT_URI'].'?action=login&idp='.$idp.'&client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&state='.$state;
                        setcookie('SPIDPHP_PROXYRETURNTO', $returnTo, time()+60*5, '/');
                        $spidsdk->login($idp, $spidcie_level, $_SERVER['SCRIPT_URI'], $atcs_index);
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
            $return .= (strpos($return, '?') !== false)? '&state='.$state : '?state='.$state;

            $service = "spid";
            if($idp=="CIE" || $idp=="CIE TEST") $service = "cie";
        
            if(isset($clients[$client_id]['service'])) {
                $service = $clients[$client_id]['service'];
            }

            $spidsdk = new SPID_PHP($production, $service);

            if($spidsdk->isAuthenticated()) {
                /* 
                 * Uncomment to exec local logout instead of IdP logout
                 */
                /*
                $sspSession = \SimpleSAML\Session::getSessionFromRequest();
                $sspSession->doLogout($service);
                header("location: " . $return);
                */

                $idp = $spidsdk->getIdPKey();

                if($idp=='EIDAS' || $idp=='EIDAS QA') {

                    $sspSession = \SimpleSAML\Session::getSessionFromRequest();
                    $sspSession->doLogout('spid');
                    header("location: " . $return);

                } else {

                    $spidsdk->logout();
                }
                
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
                if($decrypt && isset($payload_obj->data)) {
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


    $returnTo = $_COOKIE['SPIDPHP_PROXYRETURNTO'];
    if($returnTo!=null && $returnTo!='') {
        unset($_COOKIE['SPIDPHP_PROXYRETURNTO']); 
        setcookie('SPIDPHP_PROXYRETURNTO', null, -1, '/'); 
        header('Location: '.$returnTo);
        echo "Redirect to <a href='".$returnTo."'>".$returnTo."</a>";
        die();
    }

    http_response_code(404);
    if(DEBUG) echo "action not valid"; 
    die(); 


?>

