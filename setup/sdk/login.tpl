<?php 
    require_once("{{SDKHOME}}/spid-php-sdk.php");
    $spidsdk = new SPID_PHP_SDK(1);

    if(!$spidsdk->isAuthenticated()) {
        $spidsdk->insertSPIDButton("L");        
    } else {

        foreach($spidsdk->getAttributes() as $attribute=>$value) {
            echo "<br/>" . $attribute . ": <b>" . $value[0] . "</b>";
        }

        echo "<br/><br/><a href='" . $spidsdk->getLogoutURL() . "'>ESCI</a>"; 
    } 
?>