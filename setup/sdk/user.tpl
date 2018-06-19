<?php 
    require_once("{{SDKHOME}}/spid-php.php");
    $spidsdk = new SPID_PHP(1);
    $spidsdk->requireAuth();
?>

<p>ACCOUNT UTENTE</p>


<?php

    foreach($spidsdk->getAttributes() as $attribute=>$value) {
        echo "<br/>" . $attribute . ": <b>" . $value[0] . "</b>";
    }

    echo "<br/><br/><a href='" . $spidsdk->getLogoutURL() . "'>ESCI</a>"; 
?>
