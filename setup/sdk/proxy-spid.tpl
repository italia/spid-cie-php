<?php
    require_once("{{SDKHOME}}/proxy-spid-php.php");
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
    const DEFAULT_SECRET = "";
    const DEFAULT_TOKEN_EXPIRATION_TIME = 1200;
    const DEBUG = false;

    $proxy_config = file_exists(PROXY_CONFIG_FILE)? json_decode(file_get_contents(PROXY_CONFIG_FILE), true) : array();

    $spidsdk        = new SPID_PHP();
    $clients        = $proxy_config['clients'];
    $action         = $_GET['action'];
    $client_id      = $_GET['client_id'];
    $redirect_uri   = $_GET['redirect_uri'];
    $state          = $_GET['state'];
    $idp            = $_GET['idp'];


    switch($action) {

        case "login":

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

                        echo "<form name='spidauth' action='".$redirect_uri."' method='POST'>";

                        // dearray values
                        $data = array();
                        foreach($spidsdk->getAttributes() as $attribute=>$value) {
                            $data[$attribute] = $value[0];
                        }

                        if($proxy_config['signProxyResponse']) {

                            $exp_time = $proxy_config['tokenExpTime'] ?: DEFAULT_TOKEN_EXPIRATION_TIME;
                            $iss = $proxy_config['spDomain'];
                            $aud = $redirect_uri;
                            $jwk_pem = TOKEN_PRIVATE_KEY;

                            if($proxy_config['encryptProxyResponse']) {
                                $secret = $proxy_config['clients'][$client_id]['client_secret'];
                                $data = makeJWE($data, $exp_time, $iss, $aud, $secret);
                            }

                            $signedDataToken = makeJWS($data, $exp_time, $iss, $aud, $jwk_pem);
                            echo "<input type='hidden' name='data' value='".$signedDataToken."' />";

                        } else {
                            foreach($data as $attribute=>$value) {
                                echo "<input type='hidden' name='".$attribute."' value='".$value."' />";
                            }
                        }
                        echo "<input type='hidden' name='state' value='".$state."' />";
                        echo "</form>";
                        echo "<script type='text/javascript'>";
                        echo "  document.spidauth.submit();";
                        echo "</script>";
                
                    } else {
                        $spidsdk->login($idp, 1);
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
            if($spidsdk->isAuthenticated()) {
                $spidsdk->logout();
                die();
            } else {
                header("location: " . $clients[$client_id][0]);
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


    
    function makeJWE($payload, $exp_time, $iss, $aud, $secret): string {
        
        $iat        = new DateTimeImmutable();
        $exp_time   = $exp_time?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $exp        = $iat->modify("+".$exp_time." seconds")->getTimestamp();

        $data = [
            'iss'  => $iss,                                     // Issuer - spDomain
            'aud'  => $aud,                                     // Audience - Redirect_uri
            'iat'  => $iat->getTimestamp(),                     // Issued at: time when the token was generated
            'nbf'  => $iat->getTimestamp(),                     // Not before
            'exp'  => $exp,                                     // Expire
            'data' => $payload,                                 // Authentication Data
        ];

        $keyEncryptionAlgorithmManager = new AlgorithmManager([ new A256KW() ]); 
        $contentEncryptionAlgorithmManager = new AlgorithmManager([ new A256CBCHS512() ]);
        $compressionMethodManager = new CompressionMethodManager([ new Deflate() ]);

        $jweBuilder = new JWEBuilder(
            $keyEncryptionAlgorithmManager,
            $contentEncryptionAlgorithmManager,
            $compressionMethodManager
        );

        $jwk = JWKFactory::createFromSecret($secret?:DEFAULT_SECRET);

        $jwe = $jweBuilder
            ->create()
            ->withPayload(json_encode($data))
            ->withSharedProtectedHeader([
                'alg' => 'A256KW',
                'enc' => 'A256CBC-HS512',
                'zip' => 'DEF'
            ])
            ->addRecipient($jwk) 
            ->build();

        $serializer = new JWESerializer();
        $token = $serializer->serialize($jwe, 0); 

        return $token;
    }

    function makeJWS($payload, $exp_time, $iss, $aud, $jwk_pem): string {
        
        $iat        = new DateTimeImmutable();
        $exp_time   = $exp_time?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $exp        = $iat->modify("+".$exp_time." seconds")->getTimestamp();

        $data = [
            'iss'  => $iss,                                     // Issuer - spDomain
            'aud'  => $aud,                                     // Audience - Redirect_uri
            'iat'  => $iat->getTimestamp(),                     // Issued at: time when the token was generated
            'nbf'  => $iat->getTimestamp(),                     // Not before
            'exp'  => $exp,                                     // Expire
            'data' => $payload,                                 // Authentication Data
        ];

        $algorithmManager = new AlgorithmManager([new RS256()]);
        $jwk = JWKFactory::createFromKeyFile($jwk_pem);
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $jws = $jwsBuilder
            ->create() 
            ->withPayload(json_encode($data)) 
            ->addSignature($jwk, ['alg' => 'RS256']) 
            ->build(); 
        
        $serializer = new JWSSerializer(); 
        $token = $serializer->serialize($jws, 0); 
    
        return $token;
    }

?>

