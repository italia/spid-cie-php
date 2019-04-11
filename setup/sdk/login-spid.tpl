<?php 

    require_once("{{SDKHOME}}/spid-php.php");
    $spidsdk = new SPID_PHP();

    if($spidsdk->isAuthenticated() 
        && isset($_GET['idp']) 
        && $spidsdk->isIdP($_GET['idp'])) {

            echo "<p>IdP: <b>" . $spidsdk->getIdP() . "</b></p>";
            
            foreach($spidsdk->getAttributes() as $attribute=>$value) {
                echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
            }
    
            echo "<hr/><p><a href='" . $spidsdk->getLogoutURL() . "'>Logout</a></p>";

    } else {

        if(!isset($_GET['idp'])) {    
            $spidsdk->insertSPIDButtonCSS();
            $spidsdk->insertSPIDButton("L");  
            $spidsdk->insertSPIDButtonJS(); 
        } else {
            $spidsdk->login($_GET['idp'], 1);  
        }
    }
?>

