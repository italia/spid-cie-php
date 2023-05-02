<?php 

    require_once("{{SDKHOME}}/spid-php.php");

    $production = false;

    $service = "spid";
    $idp = $_REQUEST['idp'];
    if($idp=="CIE" || $idp=="CIE TEST") $service = "cie";


    $spidsdk = new SPID_PHP($production, $service);

    //$spidsdk->setPurpose("P");

    if($spidsdk->isAuthenticated() 
        && isset($idp) 
        && $spidsdk->isIdP($idp)) {

            echo "<p>IdP: <b>" . $spidsdk->getIdP() . "</b></p>";
            echo "<p>Response ID: " . $spidsdk->getResponseID() . "</p>";
            
            foreach($spidsdk->getAttributes() as $attribute=>$value) {
                echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
            }
    
            echo "<hr/><p><a href='" . $spidsdk->getLogoutURL("/login-spid.php") . "'>Logout</a></p>";

    } else {

        if(!isset($idp)) {  
        
            if($spidsdk->isSPIDEnabled()) {
            
                $spidsdk->insertSPIDButtonCSS();
			
                echo "<p>SPID BUTTON (POST Version)</p>";
                $spidsdk->insertSPIDButton("L","POST");

                echo "<p>SPID BUTTON (GET Version)</p>";
                $spidsdk->insertSPIDButton("L");

                $spidsdk->insertSPIDButtonJS(); 
            }
            
            if($spidsdk->isCIEEnabled()) {
                echo "<p>CIE BUTTON</p>";
                $spidsdk->insertCIEButton();
            }
            
        } else {
            $spidsdk->login($idp, 2);  

            // set AttributeConsumingServiceIndex 2
            //$spidsdk->login($idp, 2, "", 2);

            // set AttributeConsumingServiceIndex 2 and use HTTP-Post Binding
            //$spidsdk->login($idp, 2, "", 2, true);
        }
    }
?>

