<?php

namespace setup;

use Composer\Script\Event;
use Colors;

class Setup {

    public static function setup(Event  $event) {
        $colors = new Colors();

        echo shell_exec("clear");
        echo $colors->getColoredString("SPID PHP SDK Setup\nversion 1.0\n\n", "green");

        // retrieve path and inputs
        $_homeDir = shell_exec('echo -n "$HOME"');
        $_wwwDir = shell_exec('echo -n "$HOME/public_html"');
        $_curDir = getcwd();
        $_serviceName = "myservice";
        $_entityID = "https://localhost";
        $_acsIndex = 0;

        echo "Please insert path for current directory (" . $colors->getColoredString($_curDir, "green") . "): ";
        $curDir = readline();
        if($curDir==null || $curDir=="") $curDir = $_curDir;

        echo "Please insert path for web root directory (" . $colors->getColoredString($_wwwDir, "green") . "): ";
        $wwwDir = readline();
        if($wwwDir==null || $wwwDir=="") $wwwDir = $_wwwDir;
        
        echo "Please insert name for service endpoint (" . $colors->getColoredString($_serviceName, "green") . "): ";
        $serviceName = readline();
        if($serviceName==null || $serviceName=="") $serviceName = $_serviceName;

        echo "Please insert your EntityID (" . $colors->getColoredString($_entityID, "green") . "): ";
        $entityID = readline();
        if($entityID==null || $entityID=="") $entityID = $_entityID;

        echo "Please insert your Attribute Consuming Service Index (" . $colors->getColoredString($_acsIndex, "green") . "): ";
        $acsIndex = readline();
        if($acsIndex==null || $acsIndex=="") $acsIndex = $_acsIndex;

        echo "Add configuration for Test IDP idp.spid.gov.it ? (" . $colors->getColoredString("Y", "green") . "): ";
        $addTestIDP = readline();
        $addTestIDP = ($addTestIDP!=null && strtoupper($addTestIDP)=="N")? false:true;

        echo "Add example php file to www ? (" . $colors->getColoredString("Y", "green") . "): ";
        $addExamples = readline();
        $addExamples = ($addExamples!=null && strtoupper($addExamples)=="N")? false:true;
        
        echo $colors->getColoredString("\nCurrent directory: " . $curDir, "yellow");
        echo $colors->getColoredString("\nWeb root directory: " . $wwwDir, "yellow");
        echo $colors->getColoredString("\nService Name: " . $serviceName, "yellow");
        echo $colors->getColoredString("\nEntity ID: " . $entityID, "yellow");
        echo $colors->getColoredString("\nAttribute Consuming Service Index: " . $acsIndex, "yellow");
        echo $colors->getColoredString("\nAdd configuration for Test IDP idp.spid.gov.it: ", "yellow");
        echo $colors->getColoredString(($addTestIDP)? "Y":"N", "yellow");
        
        echo "\n\n";
        
        // create vhost directory if not exists
        if(!file_exists($wwwDir)) {
            echo $colors->getColoredString("\nWebroot directory not found. Making directory " . $wwwDir, "yellow");
            echo $colors->getColoredString("\nPlease remember to configure your virtual host.\n\n", "yellow");
            shell_exec("mkdir " . $wwwDir);
        }

        // create log directory
        shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/log");

        // create certificates
        shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/cert");
        shell_exec("openssl req -newkey rsa:2048 -new -x509 -days 3652 -nodes -out " . 
                    $curDir . "/vendor/simplesamlphp/simplesamlphp/cert/spid-sp.crt -keyout " . 
                    $curDir . "/vendor/simplesamlphp/simplesamlphp/cert/spid-sp.pem");    
                    
        shell_exec("mkdir " . $curDir . "/cert");
        shell_exec("cp " . $curDir . "/vendor/simplesamlphp/simplesamlphp/cert/*.crt " . $curDir . "/cert");

        echo $colors->getColoredString("\n\nReady to setup. Press a key to continue or CTRL-C to exit\n", "white");
        readline();        
        
        // set link to simplesamlphp      
        echo $colors->getColoredString("\nCreate symlink for simplesamlphp service... ", "white");  
        symlink($curDir . "/vendor/simplesamlphp/simplesamlphp/www", $wwwDir . "/" . $serviceName);
        echo $colors->getColoredString("OK", "green");  

        // customize and copy config file
        echo $colors->getColoredString("\nWrite config file... ", "white");  
        $vars = array("{{BASEURLPATH}}"=> "'".$serviceName."/'");
        $template = file_get_contents($curDir.'/setup/config/config.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/config/config.php", $customized);
        echo $colors->getColoredString("OK", "green");  
        
        // customize and copy authsources file
        echo $colors->getColoredString("\nWrite authsources file... ", "white");  
        $vars = array("{{ENTITYID}}"=> "'".$entityID."'", "{{ACSINDEX}}"=> $acsIndex);
        $template = file_get_contents($curDir.'/setup/config/authsources.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/config/authsources.php", $customized);
        echo $colors->getColoredString("OK", "green");  
        
        // customize and copy metadata file
        $template = file_get_contents($curDir.'/setup/metadata/saml20-idp-remote.tpl', true);        

        // setup IDP configurations
        $IDPMetadata = "";

        // add configuration for test IDP
        if($addTestIDP) {
            echo $colors->getColoredString("\nWrite metadata for test IDP... ", "white");  
            $vars = array("{{ENTITYID}}"=> "'".$entityID."'");
            $template_idp_test = file_get_contents($_curDir.'/setup/metadata/saml20-idp-remote-test.ptpl', true);
            $template_idp_test = str_replace(array_keys($vars), $vars, $template_idp_test);
            $IDPMetadata .= "\n\n" . $template_idp_test;        
            echo $colors->getColoredString("OK", "green");  
        }

        // retrieve IDP metadata
        echo $colors->getColoredString("\nRetrieve configurations for production IDPs... ", "white");  
        $xml = file_get_contents('https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml'); 
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml);  
        $xml = simplexml_load_string($xml); 

        foreach($xml->EntityDescriptor as $entity) {
            $OrganizationName = trim($entity->Organization->OrganizationName);
            $OrganizationDisplayName = trim($entity->Organization->OrganizationDisplayName);
            $OrganizationURL = trim($entity->Organization->OrganizationURL);
            $IDPentityID = trim($entity->attributes()['entityID']);
            $X509Certificate = trim($entity->IDPSSODescriptor->KeyDescriptor->KeyInfo->X509Data->X509Certificate);
            $NameIDFormat = trim($entity->IDPSSODescriptor->NameIDFormat);
            
            $template_slo = file_get_contents($curDir.'/setup/metadata/slo.ptpl', true);
            foreach($entity->IDPSSODescriptor->SingleLogoutService as $slo) {
                $SLOBinding = trim($slo->attributes()['Binding']);
                $SLOLocation = trim($slo->attributes()['Location']);

                if($SLOBinding=="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect") {
                    $vars = array("{{SLOREDIRECTLOCATION}}"=> $SLOLocation);
                    $template_slo = str_replace(array_keys($vars), $vars, $template_slo);
                }
                
                if($SLOBinding=="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST") {
                    $vars = array("{{SLOPOSTLOCATION}}"=> $SLOLocation);
                    $template_slo = str_replace(array_keys($vars), $vars, $template_slo);
                }                
            }

            $template_sso = file_get_contents($curDir.'/setup/metadata/sso.ptpl', true);
            foreach($entity->IDPSSODescriptor->SingleSignOnService as $sso) {
                $SSOBinding = trim($sso->attributes()['Binding']);
                $SSOLocation = trim($sso->attributes()['Location']);

                if($SSOBinding=="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect") {
                    $vars = array("{{SSOREDIRECTLOCATION}}"=> $SSOLocation);
                    $template_sso = str_replace(array_keys($vars), $vars, $template_sso);
                }
                
                if($SSOBinding=="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST") {
                    $vars = array("{{SSOPOSTLOCATION}}"=> $SSOLocation);
                    $template_sso = str_replace(array_keys($vars), $vars, $template_sso);
                }                 
            }

            /*
            foreach($entity->IDPSSODescriptor->Attribute as $attr) {
                $friendlyName = trim($attr->attributes()['FriendlyName']);
                $name = trim($attr->attributes()['Name']);
            } 
            */             

            $icon = "spid-idp-dummy.svg";
            switch($IDPentityID) {
                case "https://loginspid.aruba.it": $icon = "spid-idp-aruba.svg"; break;
                case "https://identity.infocert.it": $icon = "spid-idp-infocertid.svg"; break;
                case "https://spid.intesa.it": $icon = "spid-idp-intesaid.svg"; break;
                case "https://idp.namirialtsp.com/idp": $icon = "spid-idp-namirialid.svg"; break;
                case "https://posteid.poste.it": $icon = "spid-idp-posteid.svg"; break;
                case "https://identity.sieltecloud.it": $icon = "spid-idp-sielteid.svg"; break;
                case "https://spid.register.it": $icon = "spid-idp-spiditalia.svg"; break;
                case "https://login.id.tim.it/affwebservices/public/saml2sso": $icon = "spid-idp-timid.svg"; break;                
            }

            $vars = array(
                "{{ENTITYID}}"=> $IDPentityID,
                "{{ICON}}"=> $icon,
                "{{SPENTITYID}}"=> $entityID,
                "{{ORGANIZATIONNAME}}"=> $OrganizationName,
                "{{ORGANIZATIONDISPLAYNAME}}"=> $OrganizationDisplayName,
                "{{ORGANIZATIONURL}}"=> $OrganizationURL,
                "{{SSO}}"=> $template_sso,
                "{{SLO}}"=> $template_slo,
                "{{NAMEIDFORMAT}}"=> $NameIDFormat,
                "{{X509CERTIFICATE}}"=> $X509Certificate
            );

            $template_idp = file_get_contents($curDir.'/setup/metadata/saml20-idp-remote.ptpl', true);
            $template_idp = str_replace(array_keys($vars), $vars, $template_idp);
    
            $IDPMetadata .= "\n\n" . $template_idp;
        }
        echo $colors->getColoredString("OK", "green");  
        
        echo $colors->getColoredString("\nWrite metadata for production IDPs... ", "white");  
        $vars = array("{{IDPMETADATA}}"=> $IDPMetadata);
        $template = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php", $template);
        echo $colors->getColoredString("OK", "green");  
        
        /*
        // customize and copy metadata file
        $vars = array("{{ENTITYID}}"=> "'".$entityID."'");
        $template = file_get_contents($_curDir.'/setup/metadata/saml20-idp-remote.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php", $customized);        
        */
        
        // overwrite template file
        echo $colors->getColoredString("\nWrite smart-button template... ", "white");  
        $vars = array("{{SERVICENAME}}"=> $serviceName);
        $template = file_get_contents($curDir.'/setup/templates/selectidp-links.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/templates/selectidp-links.php", $customized);        
        
        // overwrite smart button js file
        $vars = array("{{SERVICENAME}}"=> $serviceName);
        $template = file_get_contents($curDir.'/setup/www/js/agid-spid-enter.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/js");
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/www/js/agid-spid-enter.js", $customized);  
        echo $colors->getColoredString("OK", "green");  
        
        // copy smart button css and img
        echo $colors->getColoredString("\nCopy smart-button resurces... ", "white");  
        shell_exec("cp -rf " . $curDir . "/vendor/italia/spid-smart-button/css " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/css");
        shell_exec("cp -rf " . $curDir . "/vendor/italia/spid-smart-button/img " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/img");
        echo $colors->getColoredString("OK", "green");  
        
        // write sdk 
        echo $colors->getColoredString("\nWrite sdk helper class... ", "white");  
        $vars = array("{{SERVICENAME}}"=> $serviceName);
        $template = file_get_contents($curDir.'/setup/sdk/spid-php-sdk.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/spid-php-sdk.php", $customized);  
        echo $colors->getColoredString("OK", "green"); 

        // write example files 
        if($addExamples) {
            echo $colors->getColoredString("\nWrite example files to www (login.php & user.php)... ", "white");  
            $vars = array("{{SDKHOME}}"=> $curDir);
            $template = file_get_contents($curDir.'/setup/sdk/login.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($wwwDir . "/login.php", $customized);  
            $template = file_get_contents($curDir.'/setup/sdk/user.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($wwwDir . "/user.php", $customized);  
            echo $colors->getColoredString("OK", "green"); 
        }
        
        // reset permissions
        echo $colors->getColoredString("\nSetting directories and files permissions... ", "white");  
        shell_exec("find " . $curDir . "/. -type d -exec chmod 0755 {} \;");   
        shell_exec("find " . $curDir . "/. -type f -exec chmod 0644 {} \;");  
        shell_exec("chmod 777 " . $curDir . "/vendor/simplesamlphp/simplesamlphp/log");

        if($addExamples) {
            shell_exec("chmod 0644 " . $wwwDir . "/login.php");  
            shell_exec("chmod 0644 " . $wwwDir . "/user.php");  
        }
        echo $colors->getColoredString("OK", "green");  
            
        

        echo $colors->getColoredString("\n\nSPID PHP SDK successfully installed! Enjoy the identities\n\n", "green");
    }



    public static function remove() {
        $colors = new Colors();

        // retrieve path and inputs
        $_wwwDir = shell_exec('echo -n "$HOME/public_html"');
        $_installDir = getcwd();
        $_serviceName = "myservice";

        echo "Please insert root path where sdk is installed (" . $colors->getColoredString($_installDir, "green") . "): ";
        $installDir = readline();
        if($installDir==null || $installDir=="") $installDir = $_installDir;

        echo "Please insert path for www (" . $colors->getColoredString($_wwwDir, "green") . "): ";
        $wwwDir = readline();
        if($wwwDir==null || $wwwDir=="") $wwwDir = $_wwwDir;
        
        echo "Please insert name for service endpoint (" . $colors->getColoredString($_serviceName, "green") . "): ";
        $serviceName = readline();
        if($serviceName==null || $serviceName=="") $serviceName = $_serviceName;

        
        echo $colors->getColoredString("\nRemove vendor directory... ", "white");
        shell_exec("rm -Rf " . $installDir . "/vendor");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove cert directory... ", "white");
        shell_exec("rm -Rf " . $installDir . "/cert");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove simplesamlphp service symlink... ", "white");
        shell_exec("rm " . $wwwDir . "/" . $serviceName);
        echo $colors->getColoredString("OK", "green");   
        echo $colors->getColoredString("\nRemove sdk file... ", "white");
        shell_exec("rm " . $installDir . "/spid-php-sdk.php");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove composer lock file... ", "white");
        shell_exec("rm " . $installDir . "/composer.lock");
        echo $colors->getColoredString("OK", "green");
        
        
        echo $colors->getColoredString("\n\nSPID PHP SDK successfully removed\n\n", "green");
    }


  
}

?>
