<?php
    require_once("{{SDKHOME}}/proxy-spid-php.php");
    use Firebase\JWT\JWT;

    $proxy_config_json = "{{SDKHOME}}/spid-php-proxy.json";
    $proxy_config = file_exists($proxy_config_json)? json_decode(file_get_contents($proxy_config_json), true) : array();

    const DEFAULT_TOKEN_EXPIRATION_TIME = 1200;

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

                        if($proxy_config['encryptProxyResponse']) {
                            echo "<input type='hidden' name='data' value='".authenticatedDataAsJWT($spidsdk->getAttributes(),$proxy_config,$redirect_uri)."' />";
                        } else {
                            foreach($spidsdk->getAttributes() as $attribute=>$value) {
                                echo "<input type='hidden' name='".$attribute."' value='".$value[0]."' />";
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

    function authenticatedDataAsJWT($payload,$proxy_config,$redirect_uri): string {
        $privateKey = file_get_contents(__DIR__ . '/cert/spid-sp.pem', true);
        //$publicKey = file_get_contents(__DIR__ . '/cert/spid-sp.crt', true);

        $issuedAt   = new DateTimeImmutable();
        $tokenExpTime = $proxy_config['tokenExpTime'] ?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $expire     = $issuedAt->modify("+".$tokenExpTime." seconds")->getTimestamp();      // Add 300 days for test purposes
        $serverName = $proxy_config['spDomain'];

        $data = [
            'iss'  => $serverName,                              // Issuer - spDomain
            'aud'  => $redirect_uri,                            // Audience - Redirect_uri
            'iat'  => $issuedAt->getTimestamp(),                // Issued at: time when the token was generated
            'nbf'  => $issuedAt->getTimestamp(),                // Not before
            'exp'  => $expire,                                  // Expire
            'data' => $payload,                                 // Authentication Data
        ];

        $jwt = JWT::encode($data, $privateKey, 'RS256');
        //echo "Encoded: " . print_r($jwt, true) . "<br>";
        //$decoded = JWT::decode($jwt, $publicKey, array('RS256'));
        //echo "Decoded: " . print_r($decoded, true) . "<br>";
        return $jwt;
    }

?>

