<?php
    require_once("{{SDKHOME}}/spid-php.php");
    $spidsdk = new SPID_PHP();

    $client = {{PROXY_CLIENT_CONFIG}};

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

            if(in_array($client_id, array_keys($client))) {
                if(in_array($redirect_uri, $client[$client_id])) {

                    if($spidsdk->isAuthenticated() 
                    && isset($_GET['idp']) 
                    && $spidsdk->isIdP($_GET['idp'])) {
            
                        echo "<form name='spidauth' action='".$redirect_uri."' method='POST'>"; 
                        
                        foreach($spidsdk->getAttributes() as $attribute=>$value) {
                            echo "<input type='hidden' name='".$attribute."' value='".$value[0]."' />";
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
                header("location: " . $client[$client_id][0]);
                die();
            }

        break;
    }

    http_response_code(404);
    //echo "action not valid"; 
    die(); 

?>

