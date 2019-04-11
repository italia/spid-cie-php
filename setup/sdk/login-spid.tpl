<?php 

    require_once("{{SDKHOME}}/spid-php.php");
    $spidsdk = new SPID_PHP();

    if(!$spidsdk->isAuthenticated()) {
        if(!isset($_GET['idp'])) {

            $spidsdk->insertSPIDButtonCSS();
            $spidsdk->insertSPIDButton("L");  
            $spidsdk->insertSPIDButtonJS(); 

        } else {

            $spidsdk->login($_GET['idp'], 1);  

        }

    } else {

        $idp = $spidsdk->getIdP();
        echo "<p>IdP: <b>" . $idp . "</b></p>";
        
        foreach($spidsdk->getAttributes() as $attribute=>$value) {
            echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
        }

        echo "<hr/><p><a href='" . $spidsdk->getLogoutURL() . "'>Logout</a></p>";
    }
?>

