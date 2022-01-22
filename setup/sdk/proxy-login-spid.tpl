<?php 

    require_once("{{SDKHOME}}/proxy-spid-php.php");

    $production = false;
    $state = '';

    $spidsdk = new PROXY_SPID_PHP('{{PROXY_CLIENT_ID}}', '{{PROXY_REDIRECT_URI}}', $state, $production);

    //$spidsdk->setPurpose("P");

    if($spidsdk->isAuthenticated() 
        && isset($_GET['idp']) 
        && $spidsdk->isIdP($_GET['idp'])) {

            echo "<p>IdP: <b>" . $spidsdk->getIdP() . "</b></p>";
            
            foreach($spidsdk->getAttributes() as $attribute=>$value) {
                echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
            }
    
            echo "<hr/><p><a href='" . $spidsdk->getLogoutURL("/proxy-login-spid.php") . "'>Logout</a></p>";

    } else {

        if(!isset($_GET['idp'])) {    
            $spidsdk->insertSPIDButtonCSS();
            $spidsdk->insertSPIDButton("L");  
            $spidsdk->insertSPIDButtonJS(); 
        } else {
            $spidsdk->login($_GET['idp'], 2);  

            // set AttributeConsumingServiceIndex 2
            //$spidsdk->login($_GET['idp'], 2, "", 2);
        }
    }
?>

