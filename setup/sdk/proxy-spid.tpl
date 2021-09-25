<?php
    require_once("{{SDKHOME}}/proxy-spid-php.php");
    use Jose\Component\Core\AlgorithmManager;
    use Jose\Component\Core\JWK;
    use Jose\Component\KeyManagement\JWKFactory;
    use Jose\Component\Signature\Algorithm\RS256;  
    use Jose\Component\Signature\JWSBuilder;
    use Jose\Component\Signature\Serializer\CompactSerializer;

    const PROXY_CONFIG_FILE = "{{SDKHOME}}/spid-php-proxy.json";
    const TOKEN_PRIVATE_KEY = "{{SDKHOME}}/cert/spid-sp.pem";
    const DEFAULT_TOKEN_EXPIRATION_TIME = 1200;


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
                //echo "idp not found"; 
                die(); 
            }

            if(in_array($client_id, array_keys($clients))) {
                if(in_array($redirect_uri, $clients[$client_id])) {

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
                            echo "<input type='hidden' name='data' value='".authenticatedDataAsJWT($data, $proxy_config, $redirect_uri)."' />";
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
                    //echo "redirect_uri not found"; 
                    die();
                }

            } else {
                http_response_code(404);
                //echo "client not found"; 
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
    }

    http_response_code(404);
    //echo "action not valid"; 
    die(); 

    function authenticatedDataAsJWT($payload, $proxy_config, $redirect_uri): string {
        
        $issuedAt   = new DateTimeImmutable();
        $tokenExpTime = $proxy_config['tokenExpTime'] ?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $expire     = $issuedAt->modify("+".$tokenExpTime." seconds")->getTimestamp();
        $serverName = $proxy_config['spDomain'];

        $data = [
            'iss'  => $serverName,                              // Issuer - spDomain
            'aud'  => $redirect_uri,                            // Audience - Redirect_uri
            'iat'  => $issuedAt->getTimestamp(),                // Issued at: time when the token was generated
            'nbf'  => $issuedAt->getTimestamp(),                // Not before
            'exp'  => $expire,                                  // Expire
            'data' => $payload,                                 // Authentication Data
        ];

        $algorithmManager = new AlgorithmManager([new RS256()]);
        $jwk = JWKFactory::createFromKeyFile(TOKEN_PRIVATE_KEY);
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $jws = $jwsBuilder
            ->create() 
            ->withPayload(json_encode($data)) 
            ->addSignature($jwk, ['alg' => 'RS256']) 
            ->build(); 
        
        $serializer = new CompactSerializer(); 
        $token = $serializer->serialize($jws, 0); 
    
        return $token;
    }

?>

