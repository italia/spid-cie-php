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
        $_spName = "Service Provider Name";
        $_spDescription = "Service Provider Description";
        $_spOrganizationName = "Organization Name";
        $_spOrganizationDisplayName = "Organization Display Name";
        $_spOrganizationURL = "https://www.organization.org";
        $_entityID = "https://localhost";
        $_acsIndex = 0;

        echo "Please insert path for current directory (" . $colors->getColoredString($_curDir, "green") . "): ";
        $curDir = readline();
        if($curDir==null || $curDir=="") $curDir = $_curDir;

        echo "Please insert path for web root directory (" . $colors->getColoredString($_wwwDir, "green") . "): ";
        $wwwDir = readline();
        if($wwwDir==null || $wwwDir=="") $wwwDir = $_wwwDir;
        
        echo "Please insert name for service endpoint (" . $colors->getColoredString($_serviceName, "green") . "): ";
        $serviceName = str_replace("'", "\'", readline());
        if($serviceName==null || $serviceName=="") $serviceName = $_serviceName;

        echo "Please insert your EntityID (" . $colors->getColoredString($_entityID, "green") . "): ";
        $entityID = readline();
        if($entityID==null || $entityID=="") $entityID = $_entityID;

        echo "Please insert your Service Provider Name (" . $colors->getColoredString($_spName, "green") . "): ";
        $spName = str_replace("'", "\'", readline());
        if($spName==null || $spName=="") $spName = $_spName;

        echo "Please insert your Service Provider Description (" . $colors->getColoredString($_spDescription, "green") . "): ";
        $spDescription = str_replace("'", "\'", readline());
        if($spDescription==null || $spDescription=="") $spDescription = $_spDescription;

        echo "Please insert your Organization Name (" . $colors->getColoredString($_spOrganizationName, "green") . "): ";
        $spOrganizationName = str_replace("'", "\'", readline());
        if($spOrganizationName==null || $spOrganizationName=="") $spOrganizationName = $_spOrganizationName;

        echo "Please insert your Organization Display Name (" . $colors->getColoredString($_spOrganizationDisplayName, "green") . "): ";
        $spOrganizationDisplayName = str_replace("'", "\'", readline());
        if($spOrganizationDisplayName==null || $spOrganizationDisplayName=="") $spOrganizationDisplayName = $_spOrganizationDisplayName;

        echo "Please insert your Organization URL (" . $colors->getColoredString($_spOrganizationURL, "green") . "): ";
        $spOrganizationURL = readline();
        if($spOrganizationURL==null || $spOrganizationURL=="") $spOrganizationURL = $_spOrganizationURL;

        echo "Please insert your Attribute Consuming Service Index (" . $colors->getColoredString($_acsIndex, "green") . "): ";
        $acsIndex = readline();
        if($acsIndex==null || $acsIndex=="") $acsIndex = $_acsIndex;

        $attr = array();

        echo "Request attribute spidCode (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'spidCode'";

        echo "Request attribute name (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'name'";
        
        echo "Request attribute familyName (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'familyName'";
        
        echo "Request attribute placeOfBirth (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'placeOfBirth'";
        
        echo "Request attribute countyOfBirth (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'countyOfBirth'";
        
        echo "Request attribute dateOfBirth (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'dateOfBirth'";
        
        echo "Request attribute gender (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'gender'";
        
        echo "Request attribute companyName (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'companyName'";
        
        echo "Request attribute registeredOffice (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'registeredOffice'";
        
        echo "Request attribute fiscalNumber (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'fiscalNumber'";
        
        echo "Request attribute ivaCode (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'ivaCode'";
        
        echo "Request attribute idCard (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'idCard'";
        
        echo "Request attribute expirationDate (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'expirationDate'";
        
        echo "Request attribute mobilePhone (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'mobilePhone'";
        
        echo "Request attribute email (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'email'";
        
        echo "Request attribute address (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'address'";
        
        echo "Request attribute digitalAddress (" . $colors->getColoredString("Y", "green") . "): ";
        if(strtoupper(readline())!="N") $attr[] = "'digitalAddress'";
        

        echo "Add configuration for Public Test IDP idp.spid.gov.it ? (" . $colors->getColoredString("Y", "green") . "): ";
        $addTestIDP = readline();
        $addTestIDP = ($addTestIDP!=null && strtoupper($addTestIDP)=="N")? false:true;

        echo "Optional URI for local Test IDP (leave empty to skip) ? (): ";
        $addLocalTestIDP = readline();
        $addLocalTestIDP = $addLocalTestIDP == null ? "" : $addLocalTestIDP;
        
        echo "Add configuration for AgID Validator validator.spid.gov.it ? (" . $colors->getColoredString("Y", "green") . "): ";
        $addValidatorIDP = readline();
        $addValidatorIDP = ($addValidatorIDP!=null && strtoupper($addValidatorIDP)=="N")? false:true;

        echo "Add example php files login-spid.php to www ? (" . $colors->getColoredString("Y", "green") . "): ";
        $addExamples = readline();
        $addExamples = ($addExamples!=null && strtoupper($addExamples)=="N")? false:true;

        /*
        echo "Use SPID smart button ? (" . $colors->getColoredString("N", "green") . "): ";
        $useSmartButton = readline();
        $useSmartButton = ($useSmartButton!=null && strtoupper($useSmartButton)=="Y")? true:false;
        */
        $useSmartButton = false;

        echo $colors->getColoredString("\nCurrent directory: " . $curDir, "yellow");
        echo $colors->getColoredString("\nWeb root directory: " . $wwwDir, "yellow");
        echo $colors->getColoredString("\nService Name: " . $serviceName, "yellow");
        echo $colors->getColoredString("\nEntity ID: " . $entityID, "yellow");
        echo $colors->getColoredString("\nService Provider Name: " . $spName, "yellow");
        echo $colors->getColoredString("\nService Provider Description: " . $spDescription, "yellow");
        echo $colors->getColoredString("\nOrganization Name: " . $spOrganizationName, "yellow");
        echo $colors->getColoredString("\nOrganization Display Name: " . $spOrganizationDisplayName, "yellow");
        echo $colors->getColoredString("\nOrganization URL: " . $spOrganizationURL, "yellow");
        echo $colors->getColoredString("\nAttribute Consuming Service Index: " . $acsIndex, "yellow");
        echo $colors->getColoredString("\nAdd configuration for Test IDP idp.spid.gov.it: ", "yellow");
        echo $colors->getColoredString(($addTestIDP)? "Y":"N", "yellow");
        echo $colors->getColoredString("\nAdd configuration for local test IDP: ", "yellow");
        echo $colors->getColoredString(($addLocalTestIDP!="")? $addLocalTestIDP:"N", "yellow");
        echo $colors->getColoredString("\nAdd configuration for AgID Validator validator.spid.gov.it: ", "yellow");
        echo $colors->getColoredString(($addValidatorIDP)? "Y":"N", "yellow");
        echo $colors->getColoredString("\nAdd example php files: ", "yellow");
        echo $colors->getColoredString(($addExamples)? "Y":"N", "yellow");
        //echo $colors->getColoredString("\nUse SPID smart button: ", "yellow");
        //echo $colors->getColoredString(($useSmartButton)? "Y":"N", "yellow");
        
        
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
        shell_exec("openssl req -x509 -sha256 -days 365 -newkey rsa:2048 -nodes -out " . 
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
        $vars = array(
            "{{ENTITYID}}"=> "'".$entityID."'", 
            "{{NAME}}"=> "'".$spName."'", 
            "{{DESCRIPTION}}"=> "'".$spDescription."'", 
            "{{ORGANIZATIONNAME}}"=> "'".$spOrganizationName."'", 
            "{{ORGANIZATIONDISPLAYNAME}}"=> "'".$spOrganizationDisplayName."'", 
            "{{ORGANIZATIONURL}}"=> "'".$spOrganizationURL."'", 
            "{{ACSINDEX}}"=> $acsIndex,
            "{{ATTRIBUTES}}"=> implode(",", $attr)
        );

        $template = file_get_contents($curDir.'/setup/config/authsources.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/vendor/simplesamlphp/simplesamlphp/config/authsources.php", $customized);
        echo $colors->getColoredString("OK", "green");  
        
        // customize and copy metadata file
        $template = file_get_contents($curDir.'/setup/metadata/saml20-idp-remote.tpl', true);        

        // setup IDP configurations
        $IDPMetadata = "";
        $IDPEntities = "";        

        // add configuration for public test IDP
        if($addTestIDP) {
            echo $colors->getColoredString("\nWrite metadata for public test IDP... ", "white");  
            $vars = array("{{ENTITYID}}"=> "'".$entityID."'");
            $template_idp_test = file_get_contents($_curDir.'/setup/metadata/saml20-idp-remote-test.ptpl', true);
            $template_idp_test = str_replace(array_keys($vars), $vars, $template_idp_test);
            $IDPMetadata .= "\n\n" . $template_idp_test;    
            $IDPEntities .= "\n\t\t\t\$this->idps['TEST'] = 'https://idp.spid.gov.it';";    
            echo $colors->getColoredString("OK", "green");  
        }

        // add configuration for AgID Validator
        if($addValidatorIDP) {
            echo $colors->getColoredString("\nWrite metadata for AgID Validator... ", "white");  
            $vars = array("{{ENTITYID}}"=> "'".$entityID."'");
            $template_idp_validator = file_get_contents($_curDir.'/setup/metadata/saml20-idp-remote-validator.ptpl', true);
            $template_idp_validator = str_replace(array_keys($vars), $vars, $template_idp_validator);
            $IDPMetadata .= "\n\n" . $template_idp_validator;    
            $IDPEntities .= "\n\t\t\t\$this->idps['VALIDATOR'] = 'https://validator.spid.gov.it';";     
            echo $colors->getColoredString("OK", "green");  
        }

        // retrieve IDP metadata
        echo $colors->getColoredString("\nRetrieve configurations for production IDPs... ", "white");  
        $xml = file_get_contents('https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml'); 

        // remove tag prefixes
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml);  
        $xml = simplexml_load_string($xml); 

        // add configuration for local test IDP
        if($addLocalTestIDP != "") {
            echo $colors->getColoredString("\nRetrieve configuration for local test IDP... ", "white");  
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $xml1 = file_get_contents($addLocalTestIDP, false, stream_context_create($arrContextOptions));
            // remove tag prefixes
            $xml1 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml1);
            $xml1 = simplexml_load_string($xml1);

            /*
            echo "$xml1\n";
            $xml1 = simplexml_load_string($xml1);
            echo ($xml1 !== FALSE ? 'Valid XML' : 'Parse Error'), PHP_EOL;
            echo "xml1 = $xml1\n";
            echo print_r($xml1) . PHP_EOL;
            echo "xml1 as xml = " . $xml1->asXml(). "\n";
            // http://php.net/manual/en/simplexmlelement.addchild.php
            $myfile = fopen("before", "w") or die("Unable to open file!");
            fwrite($myfile, print_r($xml, true));
            fclose($myfile);
            $to = $xml->addChild('EntityDescriptor', $xml1);
            foreach($xml1 as $from) {
                // https://stackoverflow.com/a/4778964
                $toDom = dom_import_simplexml($to);
                $fromDom = dom_import_simplexml($from);
                $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
            }
            $myfile1 = fopen("after", "w") or die("Unable to open file!");
            fwrite($myfile1, print_r($xml, true));
            fclose($myfile1);
            */
            $xml1->Organization->OrganizationName = "LOCAL";
            $xmlDom = dom_import_simplexml($xml);
            $xmlLocalTestDom = dom_import_simplexml($xml1);
            $xmlDom->appendChild($xmlDom->ownerDocument->importNode($xmlLocalTestDom, true));
        }

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
            /*
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
            */

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
            $IDPEntities .= "\n\t\t\t\$this->idps['".str_replace("'", "", $OrganizationName)."'] = '".$IDPentityID."';";
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
        
        if($useSmartButton) {
            // overwrite template file
            echo $colors->getColoredString("\nWrite smart-button template... ", "white");  
            $vars = array("{{SERVICENAME}}"=> $serviceName);
            $template = file_get_contents($curDir.'/setup/templates/smartbutton/selectidp-links.tpl', true);
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

        } else {
            // overwrite template file
            echo $colors->getColoredString("\nWrite spid button template... ", "white");  
            shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button/css");
            shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button/img");
            shell_exec("mkdir " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button/js");
            shell_exec("cp -rf " . $curDir . "/vendor/italia/spid-sp-access-button/src/production/css " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            shell_exec("cp -rf " . $curDir . "/vendor/italia/spid-sp-access-button/src/production/img " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            shell_exec("cp -rf " . $curDir . "/vendor/italia/spid-sp-access-button/src/production/js " . $curDir . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            echo $colors->getColoredString("OK", "green"); 
        }
        
        // write sdk 
        echo $colors->getColoredString("\nWrite sdk helper class... ", "white");  
        $vars = array("{{SERVICENAME}}"=> $serviceName, "{{IDPS}}"=> $IDPEntities);
        $template = file_get_contents($curDir.'/setup/sdk/spid-php.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($curDir . "/spid-php.php", $customized);  
        echo $colors->getColoredString("OK", "green"); 

        // apply simplesalphp patch for spid compliance
        // not needed
        // shell_exec("cp -rf " . $curDir . "/setup/simplesamlphp " . $curDir . "/vendor");

        // write example files 
        if($addExamples) {
            echo $colors->getColoredString("\nWrite example files to www (login-spid.php)... ", "white");  
            $vars = array("{{SDKHOME}}"=> $curDir);
            $template = file_get_contents($curDir.'/setup/sdk/login-spid.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($wwwDir . "/login-spid.php", $customized);    
            echo $colors->getColoredString("OK", "green"); 
        }
        
        // reset permissions
        echo $colors->getColoredString("\nSetting directories and files permissions... ", "white");  
        shell_exec("find " . $curDir . "/. -type d -exec chmod 0755 {} \;");   
        shell_exec("find " . $curDir . "/. -type f -exec chmod 0644 {} \;");  
        shell_exec("chmod 777 " . $curDir . "/vendor/simplesamlphp/simplesamlphp/log");

        if($addExamples) {
            shell_exec("chmod 0644 " . $wwwDir . "/login-spid.php");  
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
        shell_exec("rm " . $installDir . "/spid-php.php");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove composer lock file... ", "white");
        shell_exec("rm " . $installDir . "/composer.lock");
        echo $colors->getColoredString("OK", "green");
        
        
        echo $colors->getColoredString("\n\nSPID PHP SDK successfully removed\n\n", "green");
    }


  
}

?>
