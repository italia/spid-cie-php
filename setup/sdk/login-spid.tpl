<?php 
    require_once("{{SDKHOME}}/spid-php.php");
    $spidsdk = new SPID_PHP();

    $spidsdk->insertSPIDButtonCSS();

    if(!$spidsdk->isAuthenticated()) {
        if(!isset($_GET['idp'])) {
            $spidsdk->insertSPIDButton("L");               
        } else {
            $spidsdk->login($_GET['idp'], 1);                
        }
    } else {
        foreach($spidsdk->getAttributes() as $attribute=>$value) {
            echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
        }

        echo "<hr/><p><a href='" . $spidsdk->getLogoutURL() . "'>Logout</a></p>";
    }

    $spidsdk->insertSPIDButtonJS(); 
?>

