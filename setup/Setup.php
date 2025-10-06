<?php

namespace SPID_PHP;

use Composer\Script\Event;
use SPID_PHP\Colors;
use Symfony\Component\Filesystem\Filesystem;

// readline replacement
if (!function_exists('readline')) {
  function readline() {
    $fp = fopen("php://stdin", "r");
    $line = rtrim(fgets($fp, 1024));
    return $line;
  }
}

class Setup {

    public static function setup(Event $event) {
        $filesystem = new Filesystem();
        $colors = new Colors();
        $version = $event->getComposer()->getConfig()->get("version");

        if ($colors->hasColorSupport()) {
            // Clear the screen
            echo "\e[H\e[J";
        }

        echo $colors->getColoredString("SPID PHP SDK Setup\nversion " . $version . "\n\n", "green");

        // retrieve path and inputs
        $_homeDir = PHP_OS_FAMILY === "Windows"
          ? getenv("HOMEDRIVE") . getenv("HOMEPATH")
          : getenv("HOME");
        $_wwwDir = $_homeDir . "/public_html";
        $_installDir = getcwd();
        $_loggingHandler = "file";
        $_loggingDir = "log/";
        $_logFile = "simplesamlphp.log";
        $_acsCustomLocation = "";
        $_sloCustomLocation = "";
        $_storeType = "phpsession"; // default session store type
        $_storeSqlDsn = "mysql:host=localhost;dbname=saml";
        $_storeSqlUsername = "admin";
        $_storeSqlPassword = "password"; 

        $_serviceName = "myservice";
        $_spName = "Service Provider Name";
        $_spDescription = "Service Provider Description";
        $_spOrganizationName = "Organization Name";
        $_spOrganizationDisplayName = "Organization Display Name";
        $_spOrganizationURL = "https://www.organization.org";
        $_entityID = "https://localhost";
        $_spDomain = "localhost";
        $_acsIndex = 0;
        $_adminPassword = "admin";
        $_secretsalt = bin2hex(random_bytes(16)); 
        $_technicalContactName = "";
        $_technicalContactEmail = "";
        $_spCountryName = "IT";
        $_spLocalityName = "Locality";
        $_spMunicipality = "H501";
        $_spProvince = "RM";
        $_spOrganizationCodeType = "VATNumber";
        $_spOrganizationCode = "code";
        $_spOrganizationFiscalCode = "FiscalCode";
        $_spOrganizationNace2Code = "NACE2Code";
        $_spOrganizationEmailAddress = "info@organization.org"; // must be not null otherwise metadata will not generated 
        $_spOrganizationTelephoneNumber = "+3912345678"; // must be not null otherwise metadata will not generated 

        // ContactPerson billing
        $_fpaIdPaese = "IT";
        $_fpaIdCodice = "";
        $_fpaDenominazione = "";
        $_fpaIndirizzo = "";
        $_fpaNumeroCivico = "";
        $_fpaCAP = "";
        $_fpaComune = "";
        $_fpaProvincia = "";
        $_fpaNazione = "IT";
        $_fpaOrganizationName = "";
        $_fpaOrganizationEmailAddress = "info@organization.org"; // must be not null otherwise an error is raised in authentication
        $_fpaOrganizationTelephoneNumber = "+3912345678"; // must be not null otherwise an error is raised in authentication

        $config = file_exists("spid-php-setup.json") ?
                json_decode(file_get_contents("spid-php-setup.json"), true) : array();

        $config['production'] = false;

        if (!isset($config['acsCustomLocation'])) {
            $config['acsCustomLocation'] = $_acsCustomLocation;
        }
        if (!isset($config['sloCustomLocation'])) {
            $config['sloCustomLocation'] = $_sloCustomLocation;
        }

        if (!isset($config['installDir'])) {
            echo "Please insert path for current directory (" .
            $colors->getColoredString($_installDir, "green") . "): ";
            $config['installDir'] = readline();
            if ($config['installDir'] == null || $config['installDir'] == "") {
                $config['installDir'] = $_installDir;
            }
        }

        if (!isset($config['wwwDir'])) {
            echo "Please insert path for web root directory (" .
            $colors->getColoredString($_wwwDir, "green") . "): ";
            $config['wwwDir'] = readline();
            if ($config['wwwDir'] == null || $config['wwwDir'] == "") {
                $config['wwwDir'] = $_wwwDir;
            }
        }

        if (!isset($config['loggingHandler'])) {
            $loggingHandler = "";
            while ($loggingHandler != "file" && $loggingHandler != "syslog" && $loggingHandler != "errorlog") { 
                echo "Please insert the logging handler (" .
                $colors->getColoredString("file", "green") . "|syslog|errorlog): ";
                $loggingHandler = strtolower(readline());
                if ($loggingHandler == null || $loggingHandler == "") { 
                    $loggingHandler = $_loggingHandler;
                }
            }
            
            $config['loggingHandler'] = $loggingHandler;
        }

        if ($config['loggingHandler'] == 'file') {
            if (!isset($config['loggingDir'])) {
                echo "Please insert logging directory (" .
                $colors->getColoredString($_loggingDir, "green") . "): ";
                $colors->getColoredString('Remember to assign write permissions to the directory', "yellow");
                $config['loggingDir'] = readline();
                if ($config['loggingDir'] == null || $config['loggingDir'] == "") {
                    $config['loggingDir'] = $_loggingDir;
                }
            }

            if (!isset($config['logFile'])) {
                echo "Please insert log file name (" .
                $colors->getColoredString($_logFile, "green") . "): ";
                $config['logFile'] = readline();
                if ($config['logFile'] == null || $config['logFile'] == "") {
                    $config['logFile'] = $_logFile;
                }
            }

        } else {
            $config['loggingDir'] = $_loggingDir;
            $config['logFile'] = $_logFile;
        }

        if (!isset($config['serviceName'])) {
            do {                    
                echo "Please insert name for service endpoint (" .
                $colors->getColoredString($_serviceName, "green") . "): ";
                $serviceName = readline();
                if ($serviceName == null || $serviceName == "") {
                    $serviceName = $_serviceName;
                    break;
                }
                $is_valid = preg_match("/^\w{1}[-\w]*$/i", $serviceName);
                if (!$is_valid)                                
                    echo "Value '{$serviceName}' not allowed\n";

            } while(!$is_valid);
            $config['serviceName'] = $serviceName;
            
        }

        if (!isset($config['storeType'])) {
            $storeType = "";
            while ($storeType != "phpsession" && $storeType != "sqlite" && $storeType != "custom") { 
                echo "Please insert the store type (" .
                $colors->getColoredString("phpsession", "green") . "|sqlite|custom): ";
                $storeType = strtolower(readline());
                if ($storeType == null || $storeType == "") { 
                    $storeType = $_storeType;
                }
            }
            
            $config['storeType'] = $storeType;
        }

        if($config['storeType']=="sqlite") {
            $config['storeSqlDsn'] = "sqlite:" . $config['installDir'] . "/session.sqlite";
            $config['storeSqlUsername'] = "root";
            $config['storeSqlPassword'] = bin2hex(random_bytes(8));

        } else if($config['storeType']=="custom") {

            echo "Please insert the store sql dsn (" .
            $colors->getColoredString($_storeSqlDsn, "green") . "): ";
            $storeSqlDsn = strtolower(readline());
            if ($storeSqlDsn == null || $storeSqlDsn == "") { 
                $storeSqlDsn = $_storeSqlDsn;
            }                
            echo "Please insert the store sql username (" .
            $colors->getColoredString($_storeSqlUsername, "green") . "): ";
            $storeSqlUsername = strtolower(readline());
            if ($storeSqlUsername == null || $storeSqlUsername == "") { 
                $storeSqlUsername = $_storeSqlUsername;
            }
            echo "Please insert the store sql password (" .
            $colors->getColoredString($_storeSqlPassword, "green") . "): ";
            $storeSqlPassword = strtolower(readline());
            if ($storeSqlPassword == null || $storeSqlPassword == "") { 
                $storeSqlPassword = $_storeSqlPassword;
            }

            $config['storeSqlDsn'] = $storeSqlDsn;
            $config['storeSqlUsername'] = $storeSqlUsername;
            $config['storeSqlPassword'] = $storeSqlPassword;

        } else {

            $config['storeSqlDsn'] = $_storeSqlDsn;
            $config['storeSqlUsername'] = $_storeSqlUsername;
            $config['storeSqlPassword'] = $_storeSqlPassword;
        }


        if (!isset($config['entityID'])) {
            echo "Please insert your EntityID, must start with http:// or https:// (" .
            $colors->getColoredString($_entityID, "green") . "): ";
            $config['entityID'] = readline();
            if ($config['entityID'] == null || $config['entityID'] == "") {
                $config['entityID'] = $_entityID;
            }
        }

        if (!isset($config['spDomain'])) {
            $lowerEntityId = strtolower($config['entityID']);
            
            if(substr($lowerEntityId, 0, 8)==='https://') {
                $_spDomain = substr($config['entityID'], 8);
            }
    
            if(substr($lowerEntityId, 0, 7)==='http://') {
                $_spDomain = substr($config['entityID'], 7);
            }
    
            if(substr(strtolower($_spDomain), 0, 4)==='www.') {
                $_spDomain = substr($_spDomain, 4);
            }
    

            echo "Please insert your Service Provider Domain, without www. (" .
            $colors->getColoredString($_spDomain, "green") . "): ";
            $config['spDomain'] = readline();
            if ($config['spDomain'] == null || $config['spDomain'] == "") {
                $config['spDomain'] = $_spDomain;
            }

            if($config['spDomain']!=$_spDomain) {
                echo $colors->getColoredString("WARNING: the EntityID must be related to organization's domain", "yellow");
            }
        }

        if (!isset($config['spName'])) {
            echo "Please insert your Service Provider Name (" .
            $colors->getColoredString($_spName, "green") . "): ";
            $config['spName'] = str_replace("'", "\'", readline());
            if ($config['spName'] == null || $config['spName'] == "") {
                $config['spName'] = $_spName;
            }
        }

        if (!isset($config['spDescription'])) {
            echo "Please insert your Service Provider Description (" .
            $colors->getColoredString($_spDescription, "green") . "): ";
            $config['spDescription'] = str_replace("'", "\'", readline());
            if ($config['spDescription'] == null || $config['spDescription'] == "") {
                $config['spDescription'] = $_spDescription;
            }
        }

        if (!isset($config['spOrganizationName'])) {
            echo "Please insert your Organization Name (" .
            $colors->getColoredString($_spOrganizationName, "green") . "): ";
            $config['spOrganizationName'] = str_replace("'", "\'", readline());
            if ($config['spOrganizationName'] == null || $config['spOrganizationName'] == "") {
                $config['spOrganizationName'] = $_spOrganizationName;
            }
        }

        if (!isset($config['spOrganizationDisplayName'])) {
            echo "Please insert your Organization Display Name (" .
            $colors->getColoredString($_spOrganizationDisplayName, "green") . "): ";
            $config['spOrganizationDisplayName'] = str_replace("'", "\'", readline());
            if ($config['spOrganizationDisplayName'] == null || $config['spOrganizationDisplayName'] == "") {
                $config['spOrganizationDisplayName'] = $_spOrganizationDisplayName;
            }
        }

        if (!isset($config['spOrganizationURL'])) {
            echo "Please insert your Organization URL (" .
            $colors->getColoredString($_spOrganizationURL, "green") . "): ";
            $config['spOrganizationURL'] = readline();
            if ($config['spOrganizationURL'] == null || $config['spOrganizationURL'] == "") {
                $config['spOrganizationURL'] = $_spOrganizationURL;
            }
        }

        if (!isset($config['spIsPublicAdministration'])) {
            echo "Is your Organization a Public Administration (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['spIsPublicAdministration'] = readline();
            $config['spIsPublicAdministration'] = ($config['spIsPublicAdministration'] != null &&
                    strtoupper($config['spIsPublicAdministration']) == "N") ? false : true;
        }

        switch ($config['spIsPublicAdministration']) {
            case true: 
                if (!isset($config['spOrganizationCodeType']) 
                    || !isset($config['spOrganizationCode'])
                    || $config['spOrganizationCodeType']!='IPACode'
                ) {
                    echo "Please insert your Organization's IPA Code (" .
                        $colors->getColoredString($_spOrganizationCode, "green") . "): ";
                    $config['spOrganizationCode'] = readline();
                    if ($config['spOrganizationCode'] == null || $config['spOrganizationCode'] == "") {
                        $config['spOrganizationCode'] = $_spOrganizationCode;
                    }
                    $config['spOrganizationCodeType'] = "IPACode";
                    $config['spOrganizationIdentifier'] = "PA:IT-" . $config['spOrganizationCode'];
                }
                break;

            case false: 
                if (!isset($config['spOrganizationCodeType']) 
                    || !isset($config['spOrganizationCode'])
                    || (
                        $config['spOrganizationCodeType']!='VATNumber'
                        && $config['spOrganizationCodeType']!='FiscalCode'
                    )
                ) {
                    echo "Please insert 1 for VATNumber or 2 for FiscalCode (" .
                        $colors->getColoredString(($_spOrganizationCodeType=='VATNumber')? '1':'2', "green") . "): ";
                    $_organizationCodeTypeChoice = readline();
                    if ($_organizationCodeTypeChoice == null || $_organizationCodeTypeChoice == "") {
                        $_organizationCodeTypeChoice = '1';
                    }
                    if($_organizationCodeTypeChoice!='1' && $_organizationCodeTypeChoice!='2') {
                        echo "Your Organization Code type is not correctly set. It must be 1 (VATNumber) or 2 (FiscalCode). Please retry installation.\n";
                        die();
                    }
                    $config['spOrganizationCodeType'] = $_organizationCodeTypeChoice==1? 'VATNumber' : 'FiscalCode';
                    echo "Please insert your Organization's " . $config['spOrganizationCodeType'] . " (" .
                        $colors->getColoredString($_spOrganizationCode, "green") . "): ";
                    $config['spOrganizationCode'] = readline();
                    if ($config['spOrganizationCode'] == null || $config['spOrganizationCode'] == "") {
                        $config['spOrganizationCode'] = $_spOrganizationCode;
                    }
                    $config['spOrganizationIdentifier'] = ($_organizationCodeTypeChoice==1? "VATIT-" : "CF:IT-") . $config['spOrganizationCode'];
                    $_fpaIdCodice = $config['spOrganizationCode'];
                }

                if (!isset($config['fpaIdPaese'])) {
                    echo "Please insert your IdPaese for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaIdPaese, "green") . "): ";
                    $config['fpaIdPaese'] = str_replace("'", "\'", readline());
                    if ($config['fpaIdPaese'] == null || $config['fpaIdPaese'] == "") {
                        $config['fpaIdPaese'] = $_fpaIdPaese;
                    }
                }

                if (!isset($config['fpaIdCodice'])) {
                    echo "Please insert your IdCodice for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaIdCodice, "green") . "): ";
                    $config['fpaIdCodice'] = str_replace("'", "\'", readline());
                    if ($config['fpaIdCodice'] == null || $config['fpaIdCodice'] == "") {
                        $config['fpaIdCodice'] = $_fpaIdCodice;
                    }
                }

                if (!isset($config['fpaDenominazione'])) {
                    echo "Please insert your Denominazione for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaDenominazione, "green") . "): ";
                    $config['fpaDenominazione'] = str_replace("'", "\'", readline());
                    if ($config['fpaDenominazione'] == null || $config['fpaDenominazione'] == "") {
                        $config['fpaDenominazione'] = $_fpaDenominazione;
                    }
                }

                if (!isset($config['fpaIndirizzo'])) {
                    echo "Please insert your Indirizzo for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaIndirizzo, "green") . "): ";
                    $config['fpaIndirizzo'] = str_replace("'", "\'", readline());
                    if ($config['fpaIndirizzo'] == null || $config['fpaIndirizzo'] == "") {
                        $config['fpaIndirizzo'] = $_fpaIndirizzo;
                    }
                }

                if (!isset($config['fpaNumeroCivico'])) {
                    echo "Please insert your NumeroCivico for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaNumeroCivico, "green") . "): ";
                    $config['fpaNumeroCivico'] = str_replace("'", "\'", readline());
                    if ($config['fpaNumeroCivico'] == null || $config['fpaNumeroCivico'] == "") {
                        $config['fpaNumeroCivico'] = $_fpaNumeroCivico;
                    }
                }

                if (!isset($config['fpaCAP'])) {
                    echo "Please insert your CAP for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaCAP, "green") . "): ";
                    $config['fpaCAP'] = str_replace("'", "\'", readline());
                    if ($config['fpaCAP'] == null || $config['fpaCAP'] == "") {
                        $config['fpaCAP'] = $_fpaCAP;
                    }
                }

                if (!isset($config['fpaComune'])) {
                    echo "Please insert your Comune for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaComune, "green") . "): ";
                    $config['fpaComune'] = str_replace("'", "\'", readline());
                    if ($config['fpaComune'] == null || $config['fpaComune'] == "") {
                        $config['fpaComune'] = $_fpaComune;
                    }
                }

                if (!isset($config['fpaProvincia'])) {
                    echo "Please insert your Provincia for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaProvincia, "green") . "): ";
                    $config['fpaProvincia'] = str_replace("'", "\'", readline());
                    if ($config['fpaProvincia'] == null || $config['fpaProvincia'] == "") {
                        $config['fpaProvincia'] = $_fpaProvincia;
                    }
                }

                if (!isset($config['fpaNazione'])) {
                    echo "Please insert your Nazione for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaNazione, "green") . "): ";
                    $config['fpaNazione'] = str_replace("'", "\'", readline());
                    if ($config['fpaNazione'] == null || $config['fpaNazione'] == "") {
                        $config['fpaNazione'] = $_fpaNazione;
                    }
                }

                if (!isset($config['fpaOrganizationName'])) {
                    echo "Please insert your OrganizationName for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaOrganizationName, "green") . "): ";
                    $config['fpaOrganizationName'] = str_replace("'", "\'", readline());
                    if ($config['fpaOrganizationName'] == null || $config['fpaOrganizationName'] == "") {
                        $config['fpaOrganizationName'] = $_fpaOrganizationName;
                    }
                }

                if (!isset($config['fpaOrganizationEmailAddress'])) {
                    echo "Please insert your OrganizationEmailAddress for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaOrganizationEmailAddress, "green") . "): ";
                    $config['fpaOrganizationEmailAddress'] = str_replace("'", "\'", readline());
                    if ($config['fpaOrganizationEmailAddress'] == null || $config['fpaOrganizationEmailAddress'] == "") {
                        $config['fpaOrganizationEmailAddress'] = $_fpaOrganizationEmailAddress;
                    }
                }

                if (!isset($config['fpaOrganizationTelephoneNumber'])) {
                    echo "Please insert your OrganizationTelephoneNumber for CessionarioCommittente (" .
                    $colors->getColoredString($_fpaOrganizationTelephoneNumber, "green") . "): ";
                    $config['fpaOrganizationTelephoneNumber'] = str_replace("'", "\'", readline());
                    if ($config['fpaOrganizationTelephoneNumber'] == null || $config['fpaOrganizationTelephoneNumber'] == "") {
                        $config['fpaOrganizationTelephoneNumber'] = $_fpaOrganizationTelephoneNumber;
                    }
                }

                break;

            default: echo "Your Organization type is not correctly set. Please retry installation. Found: ".$config['spIsPublicAdministration']."\n";
                die();
                break;
        }

        if (!isset($config['spCountryName'])) {
            echo "Please insert your Organization's Country ISO 3166-1 code (" .
            $colors->getColoredString($_spCountryName, "green") . "): ";
            $config['spCountryName'] = readline();
            if ($config['spCountryName'] == null || $config['spCountryName'] == "") {
                $config['spCountryName'] = $_spCountryName;
            }
        }
        
        if (!isset($config['spOrganizationFiscalCode'])) {
            echo "Please insert your Organization's FiscalCode, mandatory for private (" .
            $colors->getColoredString($_spOrganizationFiscalCode, "green") . "): ";
            $config['spOrganizationFiscalCode'] = readline();
            if ($config['spOrganizationFiscalCode'] == null || $config['spOrganizationFiscalCode'] == "") {
                $config['spOrganizationFiscalCode'] = $_spOrganizationFiscalCode;
            }
        }
        
        if (!isset($config['spOrganizationNace2Code'])) {
            echo "Please insert your Organization's ATECO or NACE2, mandatory for private (" .
            $colors->getColoredString($_spOrganizationNace2Code, "green") . "): ";
            $config['spOrganizationNace2Code'] = readline();
            if ($config['spOrganizationNace2Code'] == null || $config['spOrganizationNace2Code'] == "") {
                $config['spOrganizationNace2Code'] = $_spOrganizationNace2Code;
            }
        }

        if (!isset($config['spLocalityName'])) {
            echo "Please insert your Organization's Locality Name (" .
            $colors->getColoredString($_spLocalityName, "green") . "): ";
            $config['spLocalityName'] = readline();
            if ($config['spLocalityName'] == null || $config['spLocalityName'] == "") {
                $config['spLocalityName'] = $_spLocalityName;
            }
        }
        
        if (!isset($config['spMunicipality'])) {
            echo "Please insert your Organization's ISTAT code (" .
            $colors->getColoredString($_spMunicipality, "green") . "): ";
            $config['spMunicipality'] = readline();
            if ($config['spMunicipality'] == null || $config['spMunicipality'] == "") {
                $config['spMunicipality'] = $_spMunicipality;
            }
        }

        if (!isset($config['spProvince'])) {
            echo "Please insert your Organization's Province code (" .
            $colors->getColoredString($_spProvince, "green") . "): ";
            $config['spProvince'] = readline();
            if ($config['spProvince'] == null || $config['spProvince'] == "") {
                $config['spProvince'] = $_spProvince;
            }
        }


        if (!isset($config['acsIndex'])) {
            echo "Please insert your Attribute Consuming Service Index (" .
            $colors->getColoredString($_acsIndex, "green") . "): ";
            $config['acsIndex'] = readline();
            if ($config['acsIndex'] == null || $config['acsIndex'] == "") {
                $config['acsIndex'] = $_acsIndex;
            }
        }
        
        if (!isset($config['addSPID'])) {
            echo "Setup SPID ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addSPID'] = readline();
            $config['addSPID'] = ($config['addSPID'] != null &&
                    strtoupper($config['addSPID']) == "N") ? false : true;
        }
        
        if (!isset($config['addCIE'])) {
            echo "Setup CIE ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addCIE'] = readline();
            $config['addCIE'] = ($config['addCIE'] != null &&
                    strtoupper($config['addCIE']) == "N") ? false : true;
        }
        

        if ($config['addSPID'] && (!isset($config['attr']) || count($config['attr']) == 0)) {
            $config['attr'] = array();

            echo "Request attribute spidCode (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'spidCode'";
            }

            echo "Request attribute name (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'name'";
            }

            echo "Request attribute familyName (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'familyName'";
            }

            echo "Request attribute placeOfBirth (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'placeOfBirth'";
            }

            echo "Request attribute countyOfBirth (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'countyOfBirth'";
            }

            echo "Request attribute dateOfBirth (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'dateOfBirth'";
            }

            echo "Request attribute gender (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'gender'";
            }

            echo "Request attribute companyName (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'companyName'";
            }

            echo "Request attribute registeredOffice (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'registeredOffice'";
            }

            echo "Request attribute fiscalNumber (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'fiscalNumber'";
            }

            echo "Request attribute ivaCode (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'ivaCode'";
            }

            echo "Request attribute idCard (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'idCard'";
            }

            echo "Request attribute expirationDate (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'expirationDate'";
            }

            echo "Request attribute mobilePhone (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'mobilePhone'";
            }

            echo "Request attribute email (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'email'";
            }

            echo "Request attribute address (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'address'";
            }

            echo "Request attribute digitalAddress (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'digitalAddress'";
            }

            echo "Request attribute domicileStreetAddress (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'domicileStreetAddress'";
            }

            echo "Request attribute domicilePostalCode (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'domicilePostalCode'";
            }

            echo "Request attribute domicileMunicipality (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'domicileMunicipality'";
            }

            echo "Request attribute domicileProvince (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'domicileProvince'";
            }

            echo "Request attribute domicileNation (" . $colors->getColoredString("Y", "green") . "): ";
            if (strtoupper(readline()) != "N") {
                $config['attr'][] = "'domicileNation'";
            }
        }

        if ($config['addSPID'] && !isset($config['addDemoIDP'])) {
            echo "Add configuration for Public Demo IDP demo.spid.gov.it ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addDemoIDP'] = readline();
            $config['addDemoIDP'] = ($config['addDemoIDP'] != null &&
                    strtoupper($config['addDemoIDP']) == "N") ? false : true;
        }

        if ($config['addSPID'] && !isset($config['addDemoValidatorIDP'])) {
            echo "Add configuration for Public Demo IDP demo.spid.gov.it (Validator mode) ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addDemoValidatorIDP'] = readline();
            $config['addDemoValidatorIDP'] = ($config['addDemoValidatorIDP'] != null &&
                    strtoupper($config['addDemoValidatorIDP']) == "N") ? false : true;
        }

        if ($config['addSPID'] && !isset($config['addLocalTestIDP'])) {
            echo "Optional URI for local Test IDP metadata (leave empty to skip) ? (): ";
            $config['addLocalTestIDP'] = readline();
            $config['addLocalTestIDP'] = $config['addLocalTestIDP'] == null ? "" : $config['addLocalTestIDP'];
        }

        if ($config['addSPID'] && !isset($config['addValidatorIDP'])) {
            echo "Add configuration for AgID Validator validator.spid.gov.it ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addValidatorIDP'] = readline();
            $config['addValidatorIDP'] = ($config['addValidatorIDP'] != null &&
                    strtoupper($config['addValidatorIDP']) == "N") ? false : true;
        }

        if (!isset($config['addExamples'])) {
            echo "Add example php files login.php to www ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addExamples'] = readline();
            $config['addExamples'] = ($config['addExamples'] != null &&
                    strtoupper($config['addExamples']) == "N") ? false : true;
        }

        if (!isset($config['addProxyExample'])) {
            echo "Add proxy example php files proxy.php, proxy-sample.php, proxy-login.php, error.php to www ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addProxyExample'] = readline();
            $config['addProxyExample'] = ($config['addProxyExample'] != null &&
                    strtoupper($config['addProxyExample']) == "N") ? false : true;

            if($config['addProxyExample']) {
                echo "Insert URL to register as redirect_uri (/proxy-sample.php): ";
                $proxyRedirectURI = readline();
                $proxyRedirectURI = ($proxyRedirectURI == null || $proxyRedirectURI == '')? 
                    '/proxy-sample.php' : $proxyRedirectURI;

                echo "Sign proxy response ? (" .
                    $colors->getColoredString("Y", "green") . "): ";
                $signProxyResponse = readline();
                $signProxyResponse = ($signProxyResponse != null &&
                    strtoupper($signProxyResponse) == "N") ? false : true;

                echo "Encrypt proxy response ? (" .
                    $colors->getColoredString("N", "green") . "): ";
                $encryptProxyResponse = readline();
                $encryptProxyResponse = ($encryptProxyResponse != null &&
                    strtoupper($encryptProxyResponse) == "Y") ? true : false;

                $proxyClientID = uniqid();
                echo "your client_id: " . $colors->getColoredString($proxyClientID, "red");
                echo "\n" . $colors->getColoredString("remember to configure client_id, redirect_uri and idp on button link", "green");

                $proxyClientSecret = '';
                if($encryptProxyResponse) {
                    $proxyClientSecret = uniqid();
                    echo "\nyour client_secret: " . $colors->getColoredString($proxyClientSecret, "red");
                    echo "\n" . $colors->getColoredString("use client_secret to decrypt JWE inside JWS response from proxy", "green");

                    echo "\ngrab your client_id and client_secret, then press a key to continue";

                } else {

                    echo "\ngrab your client_id, then press a key to continue";
                }

                readline();
                $config['proxyConfig'] = array(
                    'clients'=> array(
                        $proxyClientID => array(
                            "name"=> "Default client",
                            "logo"=> "/assets/img/logo.png",
                            "client_id"=> $proxyClientID,
                            "client_secret"=> $proxyClientSecret,
                            "level" => 2,
                            "atcs_index" => 0,
                            "handler" => "Plain",
                            "tokenExpTime" => 1200, //20 minutes as default
                            "response_attributes_prefix" => "",
                            "redirect_uri"=> [$proxyRedirectURI],
                        )
                    ),
                    'signProxyResponse'=> $signProxyResponse,
                    'encryptProxyResponse'=> $encryptProxyResponse
                );
            }
        }

        /*
          if (empty($config[''])) {
          echo "Use SPID smart button ? (" . $colors->getColoredString("N", "green") . "): ";
          $useSmartButton = readline();
          $useSmartButton = ($useSmartButton!=null && strtoupper($useSmartButton)=="Y")? true:false;
          }
         */
        $config['useSmartButton'] = false;

        if (!isset($config['adminPassword'])) {
            echo "Please insert password for SimpleSAMLphp (" .
              $colors->getColoredString($_adminPassword, "green") . "): ";
            $config['adminPassword'] = str_replace("'", "\'", readline());
            if ($config['adminPassword'] == null || $config['adminPassword'] == "") {
                $config['adminPassword'] = $_adminPassword;
            }
        }

        if (!isset($config['secretsalt'])) {
            echo "Please insert secretsalt for SimpleSAMLphp (" .
              $colors->getColoredString($_secretsalt, "green") . "): ";
            $config['secretsalt'] = str_replace("'", "\'", readline());
            if ($config['secretsalt'] == null || $config['secretsalt'] == "") {
                $config['secretsalt'] = $_secretsalt;
            }
        }

        /* Technical ContactPerson into metadata is not compliant with Avviso SPID n.29 v3 */
        $config['technicalContactName'] = $_technicalContactName;
        $config['technicalContactEmail'] = $_technicalContactEmail;
        /*
        if (!isset($config['technicalContactName'])) {
            echo "Please insert Tachnical Contact Name (" .
              $colors->getColoredString($_technicalContactName, "green") . "): ";
            $config['technicalContactName'] = str_replace("'", "\'", readline());
            if ($config['technicalContactName'] == null || $config['technicalContactName'] == "") {
                $config['technicalContactName'] = $_technicalContactName;
            }
        }

        if (!isset($config['technicalContactEmail'])) {
            echo "Please insert Tachnical Contact Email (" .
              $colors->getColoredString($_technicalContactEmail, "green") . "): ";
            $config['technicalContactEmail'] = str_replace("'", "\'", readline());
            if ($config['technicalContactEmail'] == null || $config['technicalContactEmail'] == "") {
                $config['technicalContactEmail'] = $_technicalContactEmail;
            }
        }
        */

        if (!isset($config['spOrganizationEmailAddress'])) {
            echo "Please insert Organization Contact Email Address (" .
              $colors->getColoredString($_spOrganizationEmailAddress, "green") . "): ";
            $config['spOrganizationEmailAddress'] = str_replace("'", "\'", readline());
            if ($config['spOrganizationEmailAddress'] == null || $config['spOrganizationEmailAddress'] == "") {
                $config['spOrganizationEmailAddress'] = $_spOrganizationEmailAddress;
            }
        }

        if (!isset($config['spOrganizationTelephoneNumber'])) {
            echo "Please insert Organization Contact Telephone Number (" .
              $colors->getColoredString($_spOrganizationTelephoneNumber, "green") . "): ";
            $config['spOrganizationTelephoneNumber'] = str_replace("'", "\'", readline());
            if ($config['spOrganizationTelephoneNumber'] == null || $config['spOrganizationTelephoneNumber'] == "") {
                $config['spOrganizationTelephoneNumber'] = $_spOrganizationTelephoneNumber;
            }
        }



        echo $colors->getColoredString("\nCurrent directory: " . $config['installDir'], "yellow");
        echo $colors->getColoredString("\nWeb root directory: " . $config['wwwDir'], "yellow");
        echo $colors->getColoredString("\nLogging Handler: " . $config['loggingHandler'], "yellow");
        if($config['loggingHandler'] == 'file') {
            echo $colors->getColoredString("\nLogging Directory: " . $config['loggingDir'], "yellow");
            echo $colors->getColoredString("\nLog File Name: " . $config['logFile'], "yellow");
        }
        echo $colors->getColoredString("\nSession Store Type: " . $config['storeType'], "yellow");

        echo $colors->getColoredString("\nProduction: " . $config['production'], "yellow");
        echo $colors->getColoredString("\nService Name: " . $config['serviceName'], "yellow");
        echo $colors->getColoredString("\nEntity ID: " . $config['entityID'], "yellow");
        echo $colors->getColoredString("\nService Provider Domain: " . $config['spDomain'], "yellow");
        echo $colors->getColoredString("\nService Provider Name: " . $config['spName'], "yellow");
        echo $colors->getColoredString("\nService Provider Description: " . $config['spDescription'], "yellow");
        echo $colors->getColoredString("\nOrganization Name: " . $config['spOrganizationName'], "yellow");
        echo $colors->getColoredString("\nOrganization Display Name: " . $config['spOrganizationDisplayName'], "yellow");
        echo $colors->getColoredString("\nOrganization URL: " . $config['spOrganizationURL'], "yellow");
        echo $colors->getColoredString("\nAttribute Consuming Service Index: " . $config['acsIndex'], "yellow");
        echo $colors->getColoredString(($config['addSPID']) ? "Y" : "N", "yellow");
        echo $colors->getColoredString(($config['addCIE']) ? "Y" : "N", "yellow");
        if($config['addSPID']) {
            echo $colors->getColoredString("\nAdd configuration for SPID Demo (demo.spid.gov.it): ", "yellow");
            echo $colors->getColoredString(($config['addDemoIDP']) ? "Y" : "N", "yellow");
            echo $colors->getColoredString("\nAdd configuration for SPID Demo Validator (demo.spid.gov.it/validator): ", "yellow");
            echo $colors->getColoredString(($config['addDemoValidatorIDP']) ? "Y" : "N", "yellow");
            echo $colors->getColoredString("\nAdd configuration for local test IDP: ", "yellow");
            echo $colors->getColoredString(($config['addLocalTestIDP'] != "") ? $config['addLocalTestIDP'] : "N", "yellow");
            echo $colors->getColoredString("\nAdd configuration for AgID Validator validator.spid.gov.it: ", "yellow");
            echo $colors->getColoredString(($config['addValidatorIDP']) ? "Y" : "N", "yellow");
        }
        echo $colors->getColoredString("\nAdd example php files: ", "yellow");
        echo $colors->getColoredString(($config['addExamples']) ? "Y" : "N", "yellow");
        echo $colors->getColoredString("\nAdd proxy example php files: ", "yellow");
        echo $colors->getColoredString(($config['addProxyExample']) ? "Y" : "N", "yellow");
        if($config['addProxyExample']) {
            echo $colors->getColoredString("\nSign proxy response: ", "yellow");
            echo $colors->getColoredString(($config['proxyConfig']['signProxyResponse']) ? "Y" : "N", "yellow");
            echo $colors->getColoredString("\nEncrypt proxy response: ", "yellow");
            echo $colors->getColoredString(($config['proxyConfig']['encryptProxyResponse']) ? "Y" : "N", "yellow");
        }
        //echo $colors->getColoredString("\nUse SPID smart button: ", "yellow");
        //echo $colors->getColoredString(($config['useSmartButton'])? "Y":"N", "yellow");
        echo $colors->getColoredString("\nSimpleSAMLphp Password: " . $config['adminPassword'], "yellow");
        echo $colors->getColoredString("\nSimpleSAMLphp secretsalt: " . $config['secretsalt'], "yellow");
        //echo $colors->getColoredString("\nTechnical Contact Name: " . $config['technicalContactName'], "yellow");
        //echo $colors->getColoredString("\nTechnical Contact Email: " . $config['technicalContactEmail'], "yellow");
        echo $colors->getColoredString("\nOrganization Contact Email Address: " . $config['spOrganizationEmailAddress'], "yellow");
        echo $colors->getColoredString("\nOrganization Contact Telephone Number: " . $config['spOrganizationTelephoneNumber'], "yellow");
        echo $colors->getColoredString("\nIs organization a Public Administration: ", "yellow");
        echo $colors->getColoredString(($config['spIsPublicAdministration']) ? "Y" : "N", "yellow");
        echo $colors->getColoredString("\nOrganization Code Type: " . $config['spOrganizationCodeType'], "yellow");
        echo $colors->getColoredString("\nOrganization Code: " . $config['spOrganizationCode'], "yellow");
        echo $colors->getColoredString("\nOrganization FiscalCode: " . $config['spOrganizationFiscalCode'], "yellow");
        echo $colors->getColoredString("\nOrganization NACE2Code: " . $config['spOrganizationNace2Code'], "yellow");
        echo $colors->getColoredString("\nOrganization Country: " . $config['spCountryName'], "yellow");
        echo $colors->getColoredString("\nOrganization Locality: " . $config['spLocalityName'], "yellow");
        echo $colors->getColoredString("\nOrganization Municipality: " . $config['spMunicipality'], "yellow");
        echo $colors->getColoredString("\nOrganization Province: " . $config['spProvince'], "yellow");
        echo $colors->getColoredString("\nOrganization Identifier: " . $config['spOrganizationIdentifier'], "yellow");
        echo $colors->getColoredString("\nCertificate CountryName: " . $config['spCountryName'], "yellow");
        echo $colors->getColoredString("\nCertificate LocalityName: " . $config['spLocalityName'], "yellow");

        if(!$config['spIsPublicAdministration']) {
            echo $colors->getColoredString("\nCessionarioCommittente IdPaese: " . $config['fpaIdPaese'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente IdCodice: " . $config['fpaIdCodice'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente Denominazione: " . $config['fpaDenominazione'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente Indirizzo: " . $config['fpaIndirizzo'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente NumeroCivico: " . $config['fpaNumeroCivico'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente CAP: " . $config['fpaCAP'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente Comune: " . $config['fpaComune'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente Provincia: " . $config['fpaProvincia'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente Nazione: " . $config['fpaNazione'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente OrganizationName: " . $config['fpaOrganizationName'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente OrganizationEmailAddress: " . $config['fpaOrganizationEmailAddress'], "yellow");
            echo $colors->getColoredString("\nCessionarioCommittente OrganizationTelephoneNumber: " . $config['fpaOrganizationTelephoneNumber'], "yellow");
        }
        
        echo "\n\n";

        // create vhost directory if not exists
        if (!file_exists($config['wwwDir'])) {
            echo $colors->getColoredString("\nWebroot directory not found. Making directory " .
                    $config['wwwDir'], "yellow");
            echo $colors->getColoredString("\nPlease remember to configure your virtual host.\n\n", "yellow");
            $filesystem->mkdir($config['wwwDir']);
        }

        // create log directory
        $filesystem->mkdir(
            $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/log"
        );

        // create SPID certificates
        if($config['addSPID']) {
		if (file_exists($config['installDir'] . "/cert/spid-sp.crt") && file_exists($config['installDir'] . "/cert/spid-sp.pem")) {
		    echo $colors->getColoredString("\nSkipping SPID certificates generation", "white");
		    $dest = $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert";
		    $filesystem->mkdir($dest);
		    $filesystem->mirror($config['installDir'] . "/cert", $dest);
		} else {
		    $filesystem->mkdir(
		        $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert"
		    );
		    echo $colors->getColoredString("\nConfiguring OpenSSL... ", "white");
		    if (!file_exists('spid-php-openssl.cnf')) {
		        $openssl_config = fopen("spid-php-openssl.cnf", "w");
		        fwrite($openssl_config, "oid_section = spid_oids\n");

		        fwrite($openssl_config, "\n[ req ]\n");
		        fwrite($openssl_config, "default_bits = 3072\n");
		        fwrite($openssl_config, "default_md = sha256\n");
		        fwrite($openssl_config, "distinguished_name = dn\n");
		        fwrite($openssl_config, "encrypt_key = no\n");
		        fwrite($openssl_config, "prompt = no\n");
		        fwrite($openssl_config, "req_extensions  = req_ext\n");

		        fwrite($openssl_config, "\n[ spid_oids ]\n");
		        //fwrite($openssl_config, "organizationIdentifier=2.5.4.97\n");
		        fwrite($openssl_config, "agidcert=1.3.76.16.6\n");
						if($config['spIsPublicAdministration']) {
							fwrite($openssl_config, "spid-publicsector-SP=1.3.76.16.4.2.1\n");
						} else {
							fwrite($openssl_config, "spid-privatesector-SP=1.3.76.16.4.3.1\n");
						}
		        fwrite($openssl_config, "uri=2.5.4.83\n");

		        fwrite($openssl_config, "\n[ dn ]\n");
		        fwrite($openssl_config, "organizationName=" . $config['spOrganizationName'] . "\n");
		        fwrite($openssl_config, "commonName=" . $config['spOrganizationDisplayName'] . "\n");
		        fwrite($openssl_config, "uri=" . $config['entityID'] . "\n");
		        fwrite($openssl_config, "organizationIdentifier=" . $config['spOrganizationIdentifier'] . "\n");
		        fwrite($openssl_config, "countryName=" . $config['spCountryName'] . "\n");
		        fwrite($openssl_config, "localityName=" . $config['spLocalityName'] . "\n");
		        //fwrite($openssl_config, "serialNumber=" . $config['spOrganizationCode'] . "\n");

		        fwrite($openssl_config, "\n[ req_ext ]\n");
		        fwrite($openssl_config, "basicConstraints=CA:FALSE\n");
		        fwrite($openssl_config, "keyUsage=critical,digitalSignature,nonRepudiation\n");
		        fwrite($openssl_config, "certificatePolicies=@agid_policies,@spid_policies\n");

		        fwrite($openssl_config, "\n[ agid_policies ]\n");
		        fwrite($openssl_config, "policyIdentifier=agidcert\n");
		        fwrite($openssl_config, "userNotice=@agidcert_notice\n");
						
						fwrite($openssl_config, "\n[ agidcert_notice ]\n");
		        fwrite($openssl_config, "explicitText=\"agIDcert\"\n");

		        fwrite($openssl_config, "\n[ spid_policies ]\n");
		        switch ($config['spIsPublicAdministration']) {
		            case true: fwrite($openssl_config, "policyIdentifier = spid-publicsector-SP\n");
		                break;
		            case false: fwrite($openssl_config, "policyIdentifier = spid-privatesector-SP\n");
		                break;

		            default:
		                echo $colors->getColoredString("Your Organization type is not correctly set. Please retry installation. Found: " . $config['spIsPublicAdministration'] . "\n", "red");
		                fwrite($openssl_config, "ERROR- Interrupted\n");
		                fclose($openssl_config);
		                die();
		                break;
		        }
		        fwrite($openssl_config, "userNotice=@spid_notice\n");
					
		        fwrite($openssl_config, "\n[ spid_notice ]\n");
						if($config['spIsPublicAdministration']) {
							fwrite($openssl_config, "explicitText=\"cert_SP_Pub\"\n");
						} else {
							fwrite($openssl_config, "explicitText=\"cert_SP_Priv\"\n");
						}
					
					echo $colors->getColoredString("OK\n", "green");
		    } 
		    shell_exec(
		            "openssl req -new -x509 -config spid-php-openssl.cnf -days 730 " .
		            " -keyout " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/spid-sp.pem" .
		            " -out " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/spid-sp.crt" . 
		            " -extensions req_ext "
		    );

		    $dest = $config['installDir'] . "/cert";
		    $filesystem->mkdir($dest);
		    $filesystem->mirror(
		        $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert",
		        $dest
		    );
		}
	}

        // create CIE certificates
        if($config['addCIE']) {
		if (file_exists($config['installDir'] . "/cert/cie-sp.crt") && file_exists($config['installDir'] . "/cert/cie-sp.pem")) {
		    echo $colors->getColoredString("\nSkipping CIE certificates generation", "white");
		    $dest = $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert";
		    $filesystem->mkdir($dest);
		    $filesystem->mirror($config['installDir'] . "/cert", $dest);
		} else {
		    $filesystem->mkdir(
		        $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert"
		    );
		    echo $colors->getColoredString("\nConfiguring OpenSSL... ", "white");
		    if (!file_exists('cie-php-openssl.cnf')) {
		        $openssl_config = fopen("cie-php-openssl.cnf", "w");
		        fwrite($openssl_config, "oid_section = cie_oids\n");

		        fwrite($openssl_config, "\n[ req ]\n");
		        fwrite($openssl_config, "default_bits = 3072\n");
		        fwrite($openssl_config, "default_md = sha256\n");
		        fwrite($openssl_config, "distinguished_name = dn\n");
		        fwrite($openssl_config, "encrypt_key = no\n");
		        fwrite($openssl_config, "prompt = no\n");
		        fwrite($openssl_config, "req_extensions  = req_ext\n");

		        fwrite($openssl_config, "\n[ cie_oids ]\n");
		        //fwrite($openssl_config, "organizationIdentifier=2.5.4.97\n");
		        fwrite($openssl_config, "uri=2.5.4.83\n");

		        fwrite($openssl_config, "\n[ dn ]\n");
		        fwrite($openssl_config, "organizationName=" . $config['spOrganizationName'] . "\n");
		        fwrite($openssl_config, "commonName=" . $config['spOrganizationDisplayName'] . "\n");
		        fwrite($openssl_config, "uri=" . $config['entityID'] . "\n");
		        fwrite($openssl_config, "organizationIdentifier=" . $config['spOrganizationIdentifier'] . "\n");
		        fwrite($openssl_config, "countryName=" . $config['spCountryName'] . "\n");
		        fwrite($openssl_config, "localityName=" . $config['spLocalityName'] . "\n");
		        //fwrite($openssl_config, "serialNumber=" . $config['spOrganizationCode'] . "\n");

		        fwrite($openssl_config, "\n[ req_ext ]\n");

		        echo $colors->getColoredString("OK\n", "green");
		    } 
		    shell_exec(
		            "openssl req -new -x509 -config cie-php-openssl.cnf -days 730 " .
		            " -keyout " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/cie-sp.pem" .
		            " -out " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/cie-sp.crt" . 
		            " -extensions req_ext "
		    );

		    $dest = $config['installDir'] . "/cert";
		    $filesystem->mkdir($dest);
		    $filesystem->mirror(
		        $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert",
		        $dest
		    );
		}
	}
	
        //echo $colors->getColoredString("\n\nReady to setup. Press a key to continue or CTRL-C to exit\n", "white");
        //readline();

        if($config['addProxyExample']) {
            self::saveProxyConfigurations($config);
        }

        file_put_contents("spid-php-setup.json", json_encode($config));

        // set link to simplesamlphp
        echo $colors->getColoredString("\nCreate symlink for simplesamlphp service... ", "white");
        $cmd_target = $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www";
        $cmd_link = $config['wwwDir'] . "/" . $config['serviceName'];
        
        $symlink_manual_creation_command = false;
        if (PHP_OS_FAMILY === "Windows") {
            // windows
            // symlink function doesn't work correctly on windows.
            // Using mklink /D needs an administrator user
            // Using mklink /J works with non-admin users, but cannot create external filesystems links
            // Here we try to make a symbolic link (/D), if it's not possibile we'll warn the user to 
            // execute the command using an administrator user.
            $cmd_target_safe_mklink = str_replace("/", "\\", $cmd_target);
            $cmd_link_safe_mklink = str_replace("/", "\\", $cmd_link);
            $mklink_cmd = "mklink /D \"$cmd_link_safe_mklink\"  \"$cmd_target_safe_mklink\"";
            try {
                $result = exec($mklink_cmd);
                if (!$result) {
                    $symlink_manual_creation_command= "WARNING! At the end of the installation execute as administrator this command:\n$mklink_cmd";
                }
            } catch(Exception $e) {
                $symlink_manual_creation_command= "WARNING! At the end of the installation execute as administrator this command:\n$mklink_cmd";
            }

        } else {
            // linux
            if (is_link($cmd_link)) {
                if (readlink($cmd_link) != $cmd_target) {
                    $symlink_manual_creation_command= "WARNING! At the end of the installation check the symbolic link at $cmd_link, it must link to $cmd_target";
                }
            }
            else {
                symlink($cmd_target, $cmd_link);
            }
        }

        if ($symlink_manual_creation_command === false) {
            echo $colors->getColoredString("OK", "green");
        } else {
            echo $colors->getColoredString("TO CREATE MANUALLY", "yellow");
        }

        // customize and copy config file
        echo $colors->getColoredString("\nWrite config file... ", "white");
        $vars = array(
            "{{BASEURLPATH}}" => "'" . $config['serviceName'] . "/'",
            "{{LOGGINGHANDLER}}" => "'" . $config['loggingHandler'] . "'",
            "{{LOGGINGDIR}}" => "'" . $config['loggingDir'] . "'",
            "{{LOGFILE}}" => "'" . $config['logFile'] . "'",
            "{{ADMIN_PASSWORD}}" => "'" . $config['adminPassword'] . "'",
            "{{SECRETSALT}}" => "'" . $config['secretsalt'] . "'",
            "{{TECHCONTACT_NAME}}" => "'" . $config['technicalContactName'] . "'",
            "{{TECHCONTACT_EMAIL}}" => "'" . $config['technicalContactEmail'] . "'",
            "{{ACSCUSTOMLOCATION}}" => "'" . $config['acsCustomLocation'] . "'",
            "{{SLOCUSTOMLOCATION}}" => "'" . $config['sloCustomLocation'] . "'",
            "{{SP_DOMAIN}}" => "'" . $config['spDomain'] . "'",
            "{{STORETYPE}}" => "'" . ($config['storeType']=='phpsession'? "phpsession" : "sql") . "'",
            "{{STORESQLDSN}}" => "'" . $config['storeSqlDsn'] . "'",
            "{{STORESQLUSERNAME}}" => "'" . $config['storeSqlUsername'] . "'",
            "{{STORESQLPASSWORD}}" => "'" . $config['storeSqlPassword'] . "'",
        );
        $template = file_get_contents($config['installDir'] . '/setup/config/config.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] .
                "/vendor/simplesamlphp/simplesamlphp/config/config.php", $customized);
        echo $colors->getColoredString("OK", "green");

        // customize and copy authsources file
        
        if($config['addSPID']) {
		echo $colors->getColoredString("\nCompile SPID authsource file... ", "white");
		$vars = array(
		    "{{ENTITYID}}" => "'" . $config['entityID'] . "'",
		    "{{NAME}}" => "'" . $config['spName'] . "'",
		    "{{DESCRIPTION}}" => "'" . $config['spDescription'] . "'",
		    "{{ORGANIZATIONNAME}}" => "'" . $config['spOrganizationName'] . "'",
		    "{{ORGANIZATIONDISPLAYNAME}}" => "'" . $config['spOrganizationDisplayName'] . "'",
		    "{{ORGANIZATIONURL}}" => "'" . $config['spOrganizationURL'] . "'",
		    "{{ACSINDEX}}" => $config['acsIndex'],
		    "{{ATTRIBUTES}}" => implode(",", $config['attr']),
		    "{{ORGANIZATIONCODETYPE}}" => "'" . $config['spOrganizationCodeType'] . "'",
		    "{{ORGANIZATIONCODE}}" => "'" . $config['spOrganizationCode'] . "'",
		    "{{ORGANIZATIONEMAILADDRESS}}" => "'" . $config['spOrganizationEmailAddress'] . "'",
		    "{{ORGANIZATIONTELEPHONENUMBER}}" => "'" . $config['spOrganizationTelephoneNumber'] . "'",
		);

		if(!$config['spIsPublicAdministration']) {
		    $vars_fpa = array(
		        "{{FPAIDPAESE}}" => "'" . $config['fpaIdPaese'] . "'",
		        "{{FPAIDCODICE}}" => "'" . $config['fpaIdCodice'] . "'",
		        "{{FPADENOMINAZIONE}}" => "'" . $config['fpaDenominazione'] . "'",
		        "{{FPAINDIRIZZO}}" => "'" . $config['fpaIndirizzo'] . "'",
		        "{{FPANUMEROCIVICO}}" => "'" . $config['fpaNumeroCivico'] . "'",
		        "{{FPACAP}}" => "'" . $config['fpaCAP'] . "'",
		        "{{FPACOMUNE}}" => "'" . $config['fpaComune'] . "'",
		        "{{FPAPROVINCIA}}" => "'" . $config['fpaProvincia'] . "'",
		        "{{FPANAZIONE}}" => "'" . $config['fpaNazione'] . "'",
		        "{{FPAORGANIZATIONNAME}}" => "'" . $config['fpaOrganizationName'] . "'",
		        "{{FPAORGANIZATIONEMAILADDRESS}}" => "'" . $config['fpaOrganizationEmailAddress'] . "'",
		        "{{FPAORGANIZATIONTELEPHONENUMBER}}" => "'" . $config['fpaOrganizationTelephoneNumber'] . "'"
		    );

		    $vars = array_merge($vars, $vars_fpa);
		}


		$template_spid_type = ($config['spIsPublicAdministration']) ? 'authsources_spid_public.tpl' : 'authsources_spid_private.tpl';
		$template_spid = file_get_contents($config['installDir'] . '/setup/config/' . $template_spid_type, true);
		$template_spid_source = str_replace(array_keys($vars), $vars, $template_spid);

		echo $colors->getColoredString("OK", "green");
        }
        
        if($config['addCIE']) {
		echo $colors->getColoredString("\nCompile CIE authsource file... ", "white");
		$vars = array(
		    "{{ENTITYID}}" => "'" . $config['entityID'] . "'",
		    "{{NAME}}" => "'" . $config['spName'] . "'",
		    "{{DESCRIPTION}}" => "'" . $config['spDescription'] . "'",
		    "{{ORGANIZATIONNAME}}" => "'" . $config['spOrganizationName'] . "'",
		    "{{ORGANIZATIONDISPLAYNAME}}" => "'" . $config['spOrganizationDisplayName'] . "'",
		    "{{ORGANIZATIONURL}}" => "'" . $config['spOrganizationURL'] . "'",
		    "{{ORGANIZATIONCODE}}" => "'" . $config['spOrganizationCode'] . "'",
		    "{{ORGANIZATIONFISCALCODE}}" => "'" . $config['spOrganizationFiscalCode'] . "'",
		    "{{ORGANIZATIONNACE2CODE}}" => "'" . $config['spOrganizationNace2Code'] . "'",
		    "{{ORGANIZATIONEMAILADDRESS}}" => "'" . $config['spOrganizationEmailAddress'] . "'",
		    "{{ORGANIZATIONTELEPHONENUMBER}}" => "'" . $config['spOrganizationTelephoneNumber'] . "'",
		    "{{ORGANIZATIONMUNICIPALITY}}" => "'" . $config['spMunicipality'] . "'",
		    "{{ORGANIZATIONPROVINCE}}" => "'" . $config['spProvince'] . "'",
		    "{{ORGANIZATIONCOUNTRY}}" => "'" . $config['spCountryName'] . "'"
		);
		
		$template_cie_type = ($config['spIsPublicAdministration']) ? 'authsources_cie_public.tpl' : 'authsources_cie_private.tpl';
		$template_cie = file_get_contents($config['installDir'] . '/setup/config/' . $template_cie_type, true);
		$template_cie_source = str_replace(array_keys($vars), $vars, $template_cie);

		echo $colors->getColoredString("OK", "green");
	}

	echo $colors->getColoredString("\nWrite authsources file... ", "white");
	$vars = array(
            "{{AUTHSOURCE_SPID}}" => $config['addSPID']? $template_spid_source : '',
            "{{AUTHSOURCE_CIE}}" => $config['addCIE']? $template_cie_source : '',
	);
        $template = file_get_contents($config['installDir'] . '/setup/config/authsources.tpl', true);
        $template_source = str_replace(array_keys($vars), $vars, $template);

        file_put_contents($config['installDir'] .
                "/vendor/simplesamlphp/simplesamlphp/config/authsources.php", $template_source);

        echo $colors->getColoredString("OK", "green");
        echo "\n\n";
        
        Setup::updateMetadata();

	if ($config['addSPID']) {
		if ($config['useSmartButton']) {
		    // overwrite template file
		    echo $colors->getColoredString("\nWrite SPID smart-button template... ", "white");
		    $vars = array("{{SERVICENAME}}" => $config['serviceName']);
		    $template = file_get_contents($config['installDir'] .
		            '/setup/templates/smartbutton/selectidp-links.tpl', true);
		    $customized = str_replace(array_keys($vars), $vars, $template);
		    file_put_contents($config['installDir'] .
		            "/vendor/simplesamlphp/simplesamlphp/templates/selectidp-links.php", $customized);

		    // overwrite smart button js file
		    $vars = array("{{SERVICENAME}}" => $config['serviceName']);
		    $template = file_get_contents($config['installDir'] . '/setup/www/js/agid-spid-enter.tpl', true);
		    $customized = str_replace(array_keys($vars), $vars, $template);
		    $filesystem->mkdir(
		        $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/js"
		    );
		    file_put_contents($config['installDir'] .
		            "/vendor/simplesamlphp/simplesamlphp/www/js/agid-spid-enter.js", $customized);
		    echo $colors->getColoredString("OK", "green");

		    // copy smart button css and img
		    echo $colors->getColoredString("\nCopy smart-button resurces... ", "white");
		    $dest = $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/css";
		    $filesystem->mkdir($dest);
		    $filesystem->mirror(
		        $config['installDir'] . "/vendor/italia/spid-smart-button/css",
		        $dest
		    );
		    $dest = $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/img";
		    $filesystem->mkdir($dest);
		    $filesystem->mirror(
		        $config['installDir'] . "/vendor/italia/spid-smart-button/img",
		        $dest
		    );
		    echo $colors->getColoredString("OK", "green");
		} else {
		    // overwrite template file
		    echo $colors->getColoredString("\nWrite SPID button template... ", "white");
		    $path = $config['installDir'] .
		        "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button";

		    foreach (["/css", "/img", "/js"] as $value) {
		        $dest = $path . $value;
		        $filesystem->mkdir($dest);
		        $filesystem->mirror(
		            $config['installDir'] . "/vendor/italia/spid-sp-access-button/src/production" . $value,
		            $dest
		        );
		    }

		    echo $colors->getColoredString("OK", "green");
		}
	}
        
	// overwrite CIE graphics
	if ($config['addCIE']) {
		echo $colors->getColoredString("\nWrite CIE graphics... ", "white");
		$path = $config['installDir'] .
			"/vendor/simplesamlphp/simplesamlphp/www/cie-graphics";

		foreach (["/SVG", "/PNG"] as $value) {
			$dest = $path . $value;
			$filesystem->mkdir($dest);
			$filesystem->mirror(
				$config['installDir'] . "/vendor/italia/cie-graphics" . $value,
				$dest
			);
		}

		echo $colors->getColoredString("OK", "green");
	}


        // apply simplesamlphp patch for spid compliance
        // needed only for templates and scripts, not for classes
        $filesystem->mirror(
            $config['installDir'] . "/setup/simplesamlphp/simplesamlphp/templates",
            $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/templates"
        );

        $filesystem->mirror(
            $config['installDir'] . "/setup/simplesamlphp/simplesamlphp/modules/saml/www/sp",
            $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/modules/saml/www/sp"
        );

        $filesystem->mirror(
            $config['installDir'] . "/setup/simplesamlphp/simplesamlphp/www/assets/icons",
            $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/assets/icons"
        );

        // write example files
        if ($config['addExamples']) {
            echo $colors->getColoredString("\nWrite example files to www (login.php)... ", "white");
            $vars = array("{{SDKHOME}}" => $config['installDir']);
            $template = file_get_contents($config['installDir'] . '/setup/sdk/login.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($config['wwwDir'] . "/login.php", $customized);
            echo $colors->getColoredString("OK", "green");
        }

        // write proxy example files
        if ($config['addProxyExample']) {
            echo $colors->getColoredString("\nWrite proxy example files to www (proxy.php, proxy-home.php, proxy-sample.php, proxy-login.php, error.php)... ", "white");

            // configuration for proxy
            $vars = self::proxyVariables($config);

            $template = file_get_contents($config['installDir'] . '/setup/sdk/proxy.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($config['wwwDir'] . "/proxy.php", $customized);

            $template = file_get_contents($config['installDir'] . '/setup/sdk/proxy-home.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($config['wwwDir'] . "/proxy-home.php", $customized);

            $template = file_get_contents($config['installDir'] . '/setup/sdk/proxy-sample.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($config['wwwDir'] . "/proxy-sample.php", $customized);

            $template = file_get_contents($config['installDir'] . '/setup/sdk/proxy-login.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($config['wwwDir'] . "/proxy-login.php", $customized);

            if (!file_exists($config['wwwDir'] . "/proxy-home.php")) {
                $template = file_get_contents($config['installDir'] . '/setup/sdk/proxy-home.tpl', true);
                $customized = str_replace(array_keys($vars), $vars, $template);
                file_put_contents($config['wwwDir'] . "/proxy-home.php", $customized);
            }

            if (!file_exists($config['wwwDir'] . "/error.php")) {
                // add error.tpl only if not exists
		        $template = file_get_contents($config['installDir'] . '/setup/sdk/error.tpl', true);
		        $customized = str_replace(array_keys($vars), $vars, $template);
		        file_put_contents($config['wwwDir'] . "/error.php", $customized);
            }

            $filesystem->mirror(
                $config['installDir'] . "/setup/www/assets",
                $config['wwwDir'] . "/assets"
            );

            echo $colors->getColoredString("OK", "green");
        }

        // reset permissions
        echo $colors->getColoredString("\nSetting directories and files permissions... ", "white");

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $config['installDir'],
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isLink()) {
              continue;
            }

            if ($item->isDir()) {
                $filesystem->chmod($item, 0755);
            } else {
                $filesystem->chmod($item, 0644);
            }
        }

        $filesystem->chmod($config['installDir'], 0755);
        $filesystem->chmod(
            $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/log",
            0777
        );

        if ($config['addExamples']) {
            $filesystem->chmod($config['wwwDir'] . "/login.php", 0644);
        }
        if ($config['addProxyExample']) {
            $filesystem->chmod($config['wwwDir'] . "/proxy.php", 0644);
            $filesystem->chmod($config['wwwDir'] . "/proxy-sample.php", 0644);
            $filesystem->chmod($config['wwwDir'] . "/proxy-login.php", 0644);
            $filesystem->chmod($config['wwwDir'] . "/error.php", 0644);
        }
        echo $colors->getColoredString("OK", "green");



        echo $colors->getColoredString("\n\nSPID PHP SDK successfully installed! Enjoy the identities\n\n", "green");
        
        if ($symlink_manual_creation_command !== false) {
            echo $colors->getColoredString("\n$symlink_manual_creation_command\n", "yellow");
        }
    }

    public static function updateMetadata() {
        $colors = new Colors();
        $_installDir = getcwd();
        $config = file_exists("spid-php-setup.json") ?
                json_decode(file_get_contents("spid-php-setup.json"), true) : array();
        
        $arrContextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => true,
            )
        );


        if (!file_exists($config['installDir'] . "/vendor")) {
            echo "\nspid-php is not installed. Please install it first.\n\n composer install\n\n";
            exit(1);
        }

        // customize and copy metadata file
        $template = file_get_contents($config['installDir'] . '/setup/metadata/saml20-idp-remote.tpl', true);

        // setup IDP configurations
        $IDPMetadata = "";
        $IDPEntities = "";

        // retrieve IdP metadatas XML from SPID Registry
        if ($config['addSPID']) {
            echo $colors->getColoredString("\nRetrieve configurations for production IDPs from SPID Registry... ", "white");
            $xml = file_get_contents('https://registry.spid.gov.it/entities-idp', false, stream_context_create($arrContextOptions));
            // remove tag prefixes
            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml);
            $xml = simplexml_load_string($xml);
            $xmlDom = dom_import_simplexml($xml);
            echo $colors->getColoredString("OK", "green");
        }

        // add configuration for CIE
        if ($config['addCIE']) {
            echo $colors->getColoredString("\nWrite metadata for CIE... ", "white");
            $vars = array("{{ENTITYID}}" => "'" . $config['entityID'] . "'");
            $template_idp_cie = file_get_contents($_installDir . '/setup/metadata/saml20-idp-remote-cie.ptpl', true);
            $template_idp_cie = str_replace(array_keys($vars), $vars, $template_idp_cie);
            $IDPMetadata .= "\n\n" . $template_idp_cie;
            $IDPEntities .= "\n\t\t\t\$this->idps['CIE'] = 'https://idserver.servizicie.interno.gov.it/idp/profile/SAML2/POST/SSO';";
            $IDPEntities .= "\n\t\t\t\$this->idps['CIE TEST'] = 'https://preproduzione.idserver.servizicie.interno.gov.it/idp/profile/SAML2/POST/SSO';";
            echo $colors->getColoredString("OK", "green");
        }
        
        // add configuration for public demo IDP
        if ($config['addSPID'] && $config['addDemoIDP']) {
            echo $colors->getColoredString("\nAdd metadata of public Demo IDP... ", "white");
            $xml1 = file_get_contents('https://demo.spid.gov.it/metadata.xml', false, stream_context_create($arrContextOptions));
            // remove tag prefixes
            $xml1 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml1);
            $xml1 = simplexml_load_string($xml1);
            $xml1->Organization->OrganizationName = "DEMO";
            $xml1Dom = dom_import_simplexml($xml1);
            $xmlDom->appendChild($xmlDom->ownerDocument->importNode($xml1Dom, true));
            echo $colors->getColoredString("OK", "green");
        }

        // add configuration for public demo IDP (Validator mode)
        if ($config['addSPID'] && $config['addDemoValidatorIDP']) {
            echo $colors->getColoredString("\nAdd metadata of public Demo IDP (Validator mode)... ", "white");
            $xml1 = file_get_contents('https://demo.spid.gov.it/validator/metadata.xml', false, stream_context_create($arrContextOptions));
            // remove tag prefixes
            $xml1 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml1);
            $xml1 = simplexml_load_string($xml1);
            $xml1->Organization->OrganizationName = "DEMOVALIDATOR";
            $xml1Dom = dom_import_simplexml($xml1);
            $xmlDom->appendChild($xmlDom->ownerDocument->importNode($xml1Dom, true));
            echo $colors->getColoredString("OK", "green");
        }

        // add configuration for AgID Validator
        if ($config['addSPID'] && $config['addValidatorIDP']) {
            echo $colors->getColoredString("\nAdd metadata of AgID Validator... ", "white");
            $xml1 = file_get_contents('https://validator.spid.gov.it/metadata.xml', false, stream_context_create($arrContextOptions));
            // remove tag prefixes
            $xml1 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml1);
            $xml1 = simplexml_load_string($xml1);
            $xml1->Organization->OrganizationName = "VALIDATOR";
            $xml1Dom = dom_import_simplexml($xml1);
            $xmlDom->appendChild($xmlDom->ownerDocument->importNode($xml1Dom, true));
            echo $colors->getColoredString("OK", "green");
        }

        // add configuration for local test IDP
        if ($config['addSPID'] && $config['addLocalTestIDP'] != "") {
            echo $colors->getColoredString("\nRetrieve configuration for local test IDP... ", "white");
            $xml1 = file_get_contents($config['addLocalTestIDP'], false, stream_context_create($arrContextOptions));
            // remove tag prefixes
            $xml1 = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml1);
            $xml1 = simplexml_load_string($xml1);
            $xml1->Organization->OrganizationName = "LOCAL";
            $xmlLocalTestDom = dom_import_simplexml($xml1);
            $xmlDom->appendChild($xmlDom->ownerDocument->importNode($xmlLocalTestDom, true));
        }

        if ($config['addSPID']) {
            foreach ($xml->EntityDescriptor as $entity) {
                $OrganizationName = trim($entity->Organization->OrganizationName);
                $OrganizationDisplayName = trim($entity->Organization->OrganizationDisplayName);
                $OrganizationURL = trim($entity->Organization->OrganizationURL);
                $IDPentityID = trim($entity->attributes()['entityID']);

                $template_keys = "array(\n";
                $nK = 0;
                $template_key = file_get_contents($config['installDir'] . '/setup/metadata/key.ptpl', true);
                foreach ($entity->IDPSSODescriptor->KeyDescriptor as $keyDescriptor) {
                    $X509Certificate = trim($keyDescriptor->KeyInfo->X509Data->X509Certificate);
                    $template_keys .= "\t\t" . $nK++ . " => ";
                    $vars = array("{{X509CERTIFICATE}}" => $X509Certificate);
                    $template_keys .= str_replace(array_keys($vars), $vars, $template_key);
                }
                $template_keys .= "\n\t)";


                $NameIDFormat = trim($entity->IDPSSODescriptor->NameIDFormat);

                $template_slo = file_get_contents($config['installDir'] . '/setup/metadata/slo.ptpl', true);
                foreach ($entity->IDPSSODescriptor->SingleLogoutService as $slo) {
                    $SLOBinding = trim($slo->attributes()['Binding']);
                    $SLOLocation = trim($slo->attributes()['Location']);

                    if ($SLOBinding == "urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect") {
                        $vars = array("{{SLOREDIRECTLOCATION}}" => $SLOLocation);
                        $template_slo = str_replace(array_keys($vars), $vars, $template_slo);
                    }

                    if ($SLOBinding == "urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST") {
                        $vars = array("{{SLOPOSTLOCATION}}" => $SLOLocation);
                        $template_slo = str_replace(array_keys($vars), $vars, $template_slo);
                    }
                }

                $template_sso = file_get_contents($config['installDir'] . '/setup/metadata/sso.ptpl', true);
                foreach ($entity->IDPSSODescriptor->SingleSignOnService as $sso) {
                    $SSOBinding = trim($sso->attributes()['Binding']);
                    $SSOLocation = trim($sso->attributes()['Location']);

                    if ($SSOBinding == "urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect") {
                        $vars = array("{{SSOREDIRECTLOCATION}}" => $SSOLocation);
                        $template_sso = str_replace(array_keys($vars), $vars, $template_sso);
                    }

                    if ($SSOBinding == "urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST") {
                        $vars = array("{{SSOPOSTLOCATION}}" => $SSOLocation);
                        $template_sso = str_replace(array_keys($vars), $vars, $template_sso);
                    }
                }

                if ($config['addCIE']) {
                    $icon = "assets/icons/spid-cie-dummy.png";
                }

                if ($config['addSPID']) {
                    $icon = "assets/icons/spid-idp-dummy.png";
                    echo $colors->getColoredString("\nRetrieve IDP logo for " . $IDPentityID . "... ", "white");
                    $registry_idp_json = file_get_contents('https://registry.spid.gov.it/entities-idp?output=json');
                    $registry_idp = json_decode($registry_idp_json, true);
        
                    foreach($registry_idp as $registry_idp_entity) {
                        if($registry_idp_entity['entity_id']==$IDPentityID) {
                            $icon = $registry_idp_entity['logo_uri'];
                        }
                    }
                }

                $vars = array(
                    "{{ENTITYID}}" => $IDPentityID,
                    "{{ICON}}" => $icon,
                    "{{SPENTITYID}}" => $config['entityID'],
                    "{{ORGANIZATIONNAME}}" => $OrganizationName,
                    "{{ORGANIZATIONDISPLAYNAME}}" => $OrganizationDisplayName,
                    "{{ORGANIZATIONURL}}" => $OrganizationURL,
                    "{{SSO}}" => $template_sso,
                    "{{SLO}}" => $template_slo,
                    "{{NAMEIDFORMAT}}" => $NameIDFormat,
                    "{{KEYS}}" => $template_keys
                );

                $template_idp = file_get_contents($config['installDir'] . '/setup/metadata/saml20-idp-remote.ptpl', true);
                $template_idp = str_replace(array_keys($vars), $vars, $template_idp);

                $IDPMetadata .= "\n\n" . $template_idp;
                $IDPEntities .= "\n\t\t\t\$this->idps['" . str_replace("'", "", $OrganizationName) . "'] = '" . $IDPentityID . "';";
            }
        }

        echo $colors->getColoredString("OK", "green");

        echo $colors->getColoredString("\nWrite metadata for production IDPs... ", "white");
        $vars = array(
            "{{IDPMETADATA}}" => $IDPMetadata
        );
        $template = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] .
                "/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php", $template);
        echo $colors->getColoredString("OK", "green");

        // write sdk
        echo $colors->getColoredString("\nWrite sdk helper class... ", "white");
        $vars = array(
            "{{SPID_ENABLED}}" => $config['addSPID']? "true":"false",
            "{{CIE_ENABLED}}" => $config['addCIE']? "true":"false",
            "{{SERVICENAME}}" => $config['serviceName'], 
            "{{IDPS}}" => $IDPEntities
        );
        $template = file_get_contents($config['installDir'] . '/setup/sdk/spid-php.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] . "/spid-php.php", $customized);

        if ($config['addProxyExample']) {
            $vars = array_merge($vars, self::proxyVariables($config));
            $template_proxy = file_get_contents($config['installDir'] . '/setup/sdk/proxy-spid-php.tpl', true);
            $customized_proxy = str_replace(array_keys($vars), $vars, $template_proxy);
            file_put_contents($config['installDir'] . "/proxy-spid-php.php", $customized_proxy);
        }

        echo $colors->getColoredString("OK", "green");
        echo "\n\n";
    }

    public static function makeCertificate($event) {
        $colors = new Colors();
        $days = 730;
        $params = $event->getArguments();

        if(count($params)>0 && is_numeric($params[0])) {
            $days = $params[0];
        }

        if(!file_exists("spid-php-openssl.cnf")) {
            echo "\nspid-php-openssl.cnf not found\n\n";
            exit(1);
        }

        $cmd = "openssl req -new -x509 -config spid-php-openssl.cnf -days " . $days .
                " -keyout ./spid-sp.pem" .
                " -out ./spid-sp.crt" . 
                " -extensions req_ext ";

        echo $colors->getColoredString("\nMaking new certificates... \n\n", "white");
        echo $colors->getColoredString($cmd, "white");

        shell_exec($cmd);
        
        echo $colors->getColoredString("\n\nprivate key and certificate generated:\n", "white");
        echo $colors->getColoredString("\nspid-sp.pem", "green");
        echo $colors->getColoredString("\nspid-sp.crt", "green");

        echo $colors->getColoredString("\n\nPlease copy spid-sp.pem and spid-sp.crt into the following paths:\n spid-php/cert\n spid-php/vendor/simplesamlphp/simplesamlphp/cert\n\n", "yellow");
    }

    public static function signMetadata($event) {
        require_once('vendor/simplesamlphp/simplesamlphp/config/authsources.php');
        require_once('vendor/simplesamlphp/simplesamlphp/lib/SimpleSAML/Metadata/Signer.php');

        $colors = new Colors();
        $params = $event->getArguments();

        echo $colors->getColoredString("\nSigning metadata... \n", "white");

        $metadata_in        = (count($params) > 0 && $params[0])? $params[0] : 'metadata.xml';
        $metadata_out       = (count($params) > 1 && $params[1])? $params[1] : 'metadata-signed.xml';
        $metadata_service   = (count($params) > 2 && $params[2])? $params[2] : 'service';
        
        echo $colors->getColoredString("\nMetadata to sign: " . $metadata_in, "white");
        echo $colors->getColoredString("\nMetadata signed: " . $metadata_out, "white");
        echo $colors->getColoredString("\nService: " . $metadata_service, "white");
        echo "\n\n";

        $config[$metadata_service]['privatekey'] = 'spid-sp.pem';
        $config[$metadata_service]['certificate'] = 'spid-sp.crt';
        
        try {
            $signer = new \SimpleSAML\Metadata\Signer();
            $xml = file_get_contents($metadata_in);
            $xml = $signer::sign($xml, $config[$metadata_service], 'SAML 2 SP');
            file_put_contents($metadata_out, $xml);
    
            echo $colors->getColoredString("Metadata signed\n\n", "green");

        } catch(Exception $e) {
            echo $colors->getColoredString("Error: " . $e->getMessage(), "red");
        }
    }

    public static function remove() {
        $filesystem = new Filesystem();
        $colors = new Colors();
        $config = file_exists("spid-php-setup.json") ?
                json_decode(file_get_contents("spid-php-setup.json"), true) : array();

        // retrieve path and inputs
        $_installDir = getcwd();
        $_homeDir = PHP_OS_FAMILY === "Windows"
          ? getenv("HOMEDRIVE") . getenv("HOMEPATH")
          : getenv("HOME");
        $_wwwDir = $_homeDir . "/public_html";
        $_serviceName = "myservice";

        if (!empty($config['installDir'])) {
            $installDir = $config['installDir'];
        } else {
            echo "Please insert root path where sdk is installed (" .
            $colors->getColoredString($_installDir, "green") . "): ";
            $installDir = readline();
            if ($installDir == null || $installDir == "") {
                $installDir = $_installDir;
            }
        }

        if (!empty($config['wwwDir'])) {
            $wwwDir = $config['wwwDir'];
        } else {
            echo "Please insert path for www (" .
            $colors->getColoredString($_wwwDir, "green") . "): ";
            $wwwDir = readline();
            if ($wwwDir == null || $wwwDir == "") {
                $wwwDir = $_wwwDir;
            }
        }

        if (!empty($config['serviceName'])) {
            $serviceName = $config['serviceName'];
        } else {
            do {                    
                echo "Please insert name for service endpoint (" .
                $colors->getColoredString($_serviceName, "green") . "): ";
                $serviceName = readline();
                if ($serviceName == null || $serviceName == "") {
                    $serviceName = $_serviceName;
                    break;
                }
                $is_valid = preg_match("/^\w{1}[-\w]*$/i", $serviceName);
                if (!$is_valid)                                
                    echo "Value '{$serviceName}' not allowed\n";

            } while(!$is_valid);

        }
		
		if (file_exists("{$installDir}/vendor/simplesamlphp/simplesamlphp/log/simplesamlphp.log")) {
			$scelta = "NOT_SET";
			$logbackupdirname = "log-backup";
			while($scelta != "Y" && $scelta != "N"){ 
				echo "\nBackup simplesamlphp.log file in {$logbackupdirname} folder? [" .
					$colors->getColoredString("Y", "green") . "/n]: ";
					$scelta = strtoupper(readline());
					if ($scelta == null || $scelta == "") { 
						$scelta ="Y";
					}
			}
			if($scelta == "Y"){
				if (!file_exists("{$installDir}/{$logbackupdirname}")) {
					echo $colors->getColoredString("\nLog backup directory not found. Making directory [" .
							"{$installDir}/{$logbackupdirname}]... ", "white");
					$filesystem->mkdir("{$installDir}/{$logbackupdirname}");
					echo $colors->getColoredString("OK", "green");
				}	
				$now = new \DateTime("now");
				echo $colors->getColoredString("\nMove simplesaml log into {$logbackupdirname} directory... ", "white");
				$filesystem->rename("{$installDir}/vendor/simplesamlphp/simplesamlphp/log/simplesamlphp.log", "{$installDir}/{$logbackupdirname}/simplesamlphp-{$now->format( 'Ymd-His' )}.log");
				echo $colors->getColoredString("OK", "green");
			}
		}

        echo $colors->getColoredString("\nRemove vendor directory [" .
                $installDir . "]... ", "white");
        $filesystem->remove($installDir . "/vendor");
        echo $colors->getColoredString("OK", "green");
        //echo $colors->getColoredString("\nRemove cert directory [" . $installDir . "/cert]... ", "white");
        //shell_exec("rm -Rf " . $installDir . "/cert");
        //echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove simplesamlphp service symlink [" .
                $wwwDir . "/" . $serviceName . "]... ", "white");
        $filesystem->remove($wwwDir . "/" . $serviceName);
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove sdk file [" .
                $installDir . "/spid-php.php]... ", "white");
        $filesystem->remove($installDir . "/spid-php.php");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove sdk proxy file [" .
        $installDir . "/proxy-spid-php.php]... ", "white");
        $filesystem->remove($installDir . "/proxy-spid-php.php");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove composer lock file... ", "white");
        $filesystem->remove($installDir . "/composer.lock");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nExample files NOT removed... ", "white");

        echo $colors->getColoredString("\n\nSPID PHP SDK successfully removed\n\n", "green");
    }

    /**
     * @param $config
     * @return array
     */
    private static function proxyVariables($config) {
        return array(
            "{{SDKHOME}}" => $config['installDir'],
            "{{PROXY_CLIENT_CONFIG}}" => var_export($config['proxyConfig'], true),
            "{{PROXY_CLIENT_ID}}" => array_keys($config['proxyConfig']['clients'])[0],
            "{{PROXY_REDIRECT_URI}}" => $config['proxyConfig']['clients'][array_keys($config['proxyConfig']['clients'])[0]]['redirect_uri'][0],
            "{{PROXY_SIGN_RESPONSE}}" => $config['proxyConfig']['signProxyResponse'],
            "{{PROXY_ENCRYPT_RESPONSE}}" => $config['proxyConfig']['encryptProxyResponse']
        );
    }

    private static function saveProxyConfigurations($config) {
        $proxy_config = file_exists("spid-php-proxy.json") ?
            json_decode(file_get_contents("spid-php-proxy.json"), true) : array();

        $proxy_config['production'] = $config['production'];
        $proxy_config['spDomain'] = $config['spDomain'];
        $configProxyClientID = array_keys($config['proxyConfig']['clients'])[0];
        $configProxyClientValue = $config['proxyConfig']['clients'][$configProxyClientID];
        $proxy_config['clients'][$configProxyClientID] = $configProxyClientValue;

        if(!array_key_exists('signProxyResponse', $proxy_config)) {
            $proxy_config['signProxyResponse'] = $config['proxyConfig']['signProxyResponse'];
        }

        if(!array_key_exists('encryptProxyResponse', $proxy_config)) {
            $proxy_config['encryptProxyResponse'] = $config['proxyConfig']['encryptProxyResponse'];
        }

        if(!array_key_exists('tokenExpTime', $proxy_config) || $proxy_config['tokenExpTime']==null) {
            $proxy_config['tokenExpTime'] = 1200; //20 minutes as default
        }
        
        file_put_contents("spid-php-proxy.json", json_encode($proxy_config));

    }

}
