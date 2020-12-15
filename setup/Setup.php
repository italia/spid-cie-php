<?php

namespace SPID_PHP;

use Composer\Script\Event;
use SPID_PHP\Colors;

class Setup {

    public static function setup(Event $event) {
        $colors = new Colors();
        $version = $event->getComposer()->getConfig()->get("version");

        echo shell_exec("clear");
        echo $colors->getColoredString("SPID PHP SDK Setup\nversion " . $version . "\n\n", "green");

        // retrieve path and inputs
        $_homeDir = shell_exec('echo -n "$HOME"');
        $_wwwDir = shell_exec('echo -n "$HOME/public_html"');
        $_installDir = getcwd();
        $_serviceName = "myservice";
        $_spName = "Service Provider Name";
        $_spDescription = "Service Provider Description";
        $_spOrganizationName = "Organization Name";
        $_spOrganizationDisplayName = "Organization Display Name";
        $_spOrganizationURL = "https://www.organization.org";
        $_entityID = "https://localhost";
        $_acsIndex = 0;
        $_adminPassword = "admin";
        $_technicalContactName = "";
        $_technicalContactEmail = "";
        $_spCountryName = "IT";
        $_spLocalityName = "";
        $_spIsPublicAdministration = "yes";
        $_spOrganizationCodeType = "";
        $_spOrganizationCode = "";
        $_spOrganizationEmailAddress = "";
        $_spOrganizationTelephoneNumber = "";

        $config = file_exists("spid-php-setup.json") ?
                json_decode(file_get_contents("spid-php-setup.json"), true) : array();

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

        if (!isset($config['serviceName'])) {
            echo "Please insert name for service endpoint (" .
            $colors->getColoredString($_serviceName, "green") . "): ";
            $config['serviceName'] = str_replace("'", "\'", readline());
            if ($config['serviceName'] == null || $config['serviceName'] == "") {
                $config['serviceName'] = $_serviceName;
            }
        }

        if (!isset($config['entityID'])) {
            echo "Please insert your EntityID (" .
            $colors->getColoredString($_entityID, "green") . "): ";
            $config['entityID'] = readline();
            if ($config['entityID'] == null || $config['entityID'] == "") {
                $config['entityID'] = $_entityID;
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
            echo "Is your Organization a Public Administration (yes/no)? (" .
            $colors->getColoredString($_spIsPublicAdministration, "green") . "): ";
            $config['spIsPublicAdministration'] = readline();
            if ($config['spIsPublicAdministration'] == null || $config['spIsPublicAdministration'] == "") {
                $config['spIsPublicAdministration'] = $_spIsPublicAdministration;
            }
        }

        switch ($config['spIsPublicAdministration']) {
            case 'yes': 
                if (!isset($config['spOrganizationCodeType']) 
                    || !isset($config['spOrganizationCode'])
                    || $config['spOrganizationCodeType']!='IPACode'
                ) {
                    echo "Please insert your Organization's IPA Code (" .
                        $colors->getColoredString($_spOrganizationCode, "green") . "): ";
                    $config['spOrganizationCode'] = readline();
                    if ($config['spOrganizationCode'] == null || $config['spOrganizationCode'] == "") {
                        $config['spOrganizationCodeType'] = "IPACode";
                        $config['spOrganizationCode'] = $_spOrganizationCode;
                    }
                    $config['spOrganizationIdentifier'] = "PA:IT-" . $config['spOrganizationCode'];
                }
                break;

            case 'no': 
                if (!isset($config['spOrganizationCodeType']) 
                    || !isset($config['spOrganizationCode'])
                    || (
                        $config['spOrganizationCodeType']!='VATNumber'
                        && $config['spOrganizationCodeType']!='FiscalCode'
                    )
                ) {
                    echo "Please insert 1 for VATNumber or 2 for FiscalCode (" .
                        $colors->getColoredString($_spOrganizationCodeType, "green") . "): ";
                    $organizationCodeTypeChoice = readline();
                    if($organizationCodeTypeChoice!='1' || $organizationCodeTypeChoice!='2') {
                        echo "Your Organization Code type is not correctly set. It must be 1 (VATNumber) or 2 (FiscalCode). Please retry installation.\n";
                        die();
                    }
                    $config['spOrganizationCodeType'] = $organizationCodeTypeChoice==1? 'VATNumber' : 'FiscalCode';
                    echo "Please insert your Organization's " . $config['spOrganizationCodeType'] . " (" .
                        $colors->getColoredString($_spOrganizationCode, "green") . "): ";
                    $config['spOrganizationCode'] = readline();
                    if ($config['spOrganizationCode'] == null || $config['spOrganizationCode'] == "") {
                        $config['spOrganizationCode'] = $_spOrganizationCode;
                    }
                    $config['spOrganizationIdentifier'] = ($organizationCodeTypeChoice==1? "VATIT-" : "CF:IT-") . $config['spOrganizationCode'];
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

        if (!isset($config['spLocalityName'])) {
            echo "Please insert your Organization's Locality Name (" .
            $colors->getColoredString($_spLocalityName, "green") . "): ";
            $config['spLocalityName'] = readline();
            if ($config['spLocalityName'] == null || $config['spLocalityName'] == "") {
                $config['spLocalityName'] = $_spLocalityName;
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

        if (!isset($config['attr']) || count($config['attr']) == 0) {
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
        }

        if (!isset($config['addTestIDP'])) {
            echo "Add configuration for Public Test IDP idp.spid.gov.it ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addTestIDP'] = readline();
            $config['addTestIDP'] = ($config['addTestIDP'] != null &&
                    strtoupper($config['addTestIDP']) == "N") ? false : true;
        }

        if (!isset($config['addLocalTestIDP'])) {
            echo "Optional URI for local Test IDP metadata (leave empty to skip) ? (): ";
            $config['addLocalTestIDP'] = readline();
            $config['addLocalTestIDP'] = $config['addLocalTestIDP'] == null ? "" : $config['addLocalTestIDP'];
        }

        if (!isset($config['addValidatorIDP'])) {
            echo "Add configuration for AgID Validator validator.spid.gov.it ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addValidatorIDP'] = readline();
            $config['addValidatorIDP'] = ($config['addValidatorIDP'] != null &&
                    strtoupper($config['addValidatorIDP']) == "N") ? false : true;
        }

        if (!isset($config['addExamples'])) {
            echo "Add example php files login-spid.php to www ? (" .
            $colors->getColoredString("Y", "green") . "): ";
            $config['addExamples'] = readline();
            $config['addExamples'] = ($config['addExamples'] != null &&
                    strtoupper($config['addExamples']) == "N") ? false : true;
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

        if (!isset($config['organizationEmailAddress'])) {
            echo "Please insert Organization Contact Email Address (" .
              $colors->getColoredString($_spOrganizationEmailAddress, "green") . "): ";
            $config['organizationEmailAddress'] = str_replace("'", "\'", readline());
            if ($config['organizationEmailAddress'] == null || $config['organizationEmailAddress'] == "") {
                $config['organizationEmailAddress'] = $_spOrganizationEmailAddress;
            }
        }

        if (!isset($config['organizationTelephoneNumber'])) {
            echo "Please insert Organization Contact Telephone Number (" .
              $colors->getColoredString($_spOrganizationTelephoneNumber, "green") . "): ";
            $config['organizationTelephoneNumber'] = str_replace("'", "\'", readline());
            if ($config['organizationTelephoneNumber'] == null || $config['organizationTelephoneNumber'] == "") {
                $config['organizationTelephoneNumber'] = $_spOrganizationTelephoneNumber;
            }
        }

        echo $colors->getColoredString("\nCurrent directory: " . $config['installDir'], "yellow");
        echo $colors->getColoredString("\nWeb root directory: " . $config['wwwDir'], "yellow");
        echo $colors->getColoredString("\nService Name: " . $config['serviceName'], "yellow");
        echo $colors->getColoredString("\nEntity ID: " . $config['entityID'], "yellow");
        echo $colors->getColoredString("\nService Provider Name: " . $config['spName'], "yellow");
        echo $colors->getColoredString("\nService Provider Description: " . $config['spDescription'], "yellow");
        echo $colors->getColoredString("\nOrganization Name: " . $config['spOrganizationName'], "yellow");
        echo $colors->getColoredString("\nOrganization Display Name: " . $config['spOrganizationDisplayName'], "yellow");
        echo $colors->getColoredString("\nOrganization URL: " . $config['spOrganizationURL'], "yellow");
        echo $colors->getColoredString("\nAttribute Consuming Service Index: " . $config['acsIndex'], "yellow");
        echo $colors->getColoredString("\nAdd configuration for Test IDP idp.spid.gov.it: ", "yellow");
        echo $colors->getColoredString(($config['addTestIDP']) ? "Y" : "N", "yellow");
        echo $colors->getColoredString("\nAdd configuration for local test IDP: ", "yellow");
        echo $colors->getColoredString(($config['addLocalTestIDP'] != "") ? $config['addLocalTestIDP'] : "N", "yellow");
        echo $colors->getColoredString("\nAdd configuration for AgID Validator validator.spid.gov.it: ", "yellow");
        echo $colors->getColoredString(($config['addValidatorIDP']) ? "Y" : "N", "yellow");
        echo $colors->getColoredString("\nAdd example php files: ", "yellow");
        echo $colors->getColoredString(($config['addExamples']) ? "Y" : "N", "yellow");
        //echo $colors->getColoredString("\nUse SPID smart button: ", "yellow");
        //echo $colors->getColoredString(($config['useSmartButton'])? "Y":"N", "yellow");
        echo $colors->getColoredString("\nSimpleSAMLphp Password: " . $config['adminPassword'], "yellow");
        //echo $colors->getColoredString("\nTechnical Contact Name: " . $config['technicalContactName'], "yellow");
        //echo $colors->getColoredString("\nTechnical Contact Email: " . $config['technicalContactEmail'], "yellow");
        echo $colors->getColoredString("\nOrganization Contact Email Address: " . $config['organizationEmailAddress'], "yellow");
        echo $colors->getColoredString("\nOrganization Contact Telephone Number: " . $config['organizationTelephoneNumber'], "yellow");
        echo $colors->getColoredString("\nIs organization a Public Administration: " . $config['spIsPublicAdministration'], "yellow");
        echo $colors->getColoredString("\nOrganization Code Type: " . $config['spOrganizationCodeType'], "yellow");
        echo $colors->getColoredString("\nOrganization Code: " . $config['spOrganizationCode'], "yellow");
        echo $colors->getColoredString("\nOrganization Identifier: " . $config['spOrganizationIdentifier'], "yellow");
        echo $colors->getColoredString("\nCertificate CountryName: " . $config['spCountryName'], "yellow");
        echo $colors->getColoredString("\nCertificate LocalityName: " . $config['spLocalityName'], "yellow");
        echo "\n\n";

        // create vhost directory if not exists
        if (!file_exists($config['wwwDir'])) {
            echo $colors->getColoredString("\nWebroot directory not found. Making directory " .
                    $config['wwwDir'], "yellow");
            echo $colors->getColoredString("\nPlease remember to configure your virtual host.\n\n", "yellow");
            shell_exec("mkdir " . $config['wwwDir']);
        }

        // create log directory
        shell_exec("mkdir " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/log");

        // create certificates
        if (file_exists($config['installDir'] . "/cert/spid-sp.crt") && file_exists($config['installDir'] . "/cert/spid-sp.pem")) {
            echo $colors->getColoredString("\nSkipping certificates generation", "white");
            shell_exec("mkdir " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert");
            shell_exec("cp " . $config['installDir'] . "/cert/* " .
                    $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert");
        } else {
            shell_exec("mkdir " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert");
            echo $colors->getColoredString("\nConfiguring OpenSSL... ", "white");
            if (!file_exists('openssl.cnf')) {
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
                fwrite($openssl_config, "organizationIdentifier=2.5.4.97\n");
                fwrite($openssl_config, "spid-privatesector-SP=1.3.76.16.4.3.1\n");
                fwrite($openssl_config, "spid-publicsector-SP=1.3.76.16.4.2.1\n");
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
                fwrite($openssl_config, "certificatePolicies = @spid_policies\n");

                fwrite($openssl_config, "\n[ spid_policies ]\n");
                switch ($config['spIsPublicAdministration']) {
                    case 'yes': fwrite($openssl_config, "policyIdentifier = spid-publicsector-SP\n");
                        break;
                    case 'no': fwrite($openssl_config, "policyIdentifier = spid-privatesector-SP\n");
                        break;

                    default:
                        echo $colors->getColoredString("Your Organization type is not correctly set. Please retry installation. Found: " . $config['spIsPublicAdministration'] . "\n", "red");
                        fwrite($openssl_config, "ERROR- Interrupted\n");
                        fclose($openssl_config);
                        die();
                        break;
                }
                echo $colors->getColoredString("OK\n", "green");
            } 
            shell_exec(
                    "openssl req -new -x509 -config spid-php-openssl.cnf -days 730 " .
                    " -keyout " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/spid-sp.pem" .
                    " -out " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/spid-sp.crt" . 
                    " -extensions req_ext "
            );

            shell_exec("mkdir " . $config['installDir'] . "/cert");
            shell_exec("cp " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/cert/* " .
                    $config['installDir'] . "/cert");
        }


        //echo $colors->getColoredString("\n\nReady to setup. Press a key to continue or CTRL-C to exit\n", "white");
        //readline();

        file_put_contents("spid-php-setup.json", json_encode($config));

        // set link to simplesamlphp
        echo $colors->getColoredString("\nCreate symlink for simplesamlphp service... ", "white");
        $cmd_target = $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www";
        $cmd_link = $config['wwwDir'] . "/" . $config['serviceName'];
        symlink($cmd_target, $cmd_link);
        echo $colors->getColoredString("OK", "green");

        // customize and copy config file
        echo $colors->getColoredString("\nWrite config file... ", "white");
        $vars = array(
            "{{BASEURLPATH}}" => "'" . $config['serviceName'] . "/'",
            "{{ADMIN_PASSWORD}}" => "'" . $config['adminPassword'] . "'",
            "{{TECHCONTACT_NAME}}" => "'" . $config['technicalContactName'] . "'",
            "{{TECHCONTACT_EMAIL}}" => "'" . $config['technicalContactEmail'] . "'"
        );
        $template = file_get_contents($config['installDir'] . '/setup/config/config.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] .
                "/vendor/simplesamlphp/simplesamlphp/config/config.php", $customized);
        echo $colors->getColoredString("OK", "green");

        // customize and copy authsources file
        echo $colors->getColoredString("\nWrite authsources file... ", "white");
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

        $template = file_get_contents($config['installDir'] . '/setup/config/authsources.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] .
                "/vendor/simplesamlphp/simplesamlphp/config/authsources.php", $customized);
        echo $colors->getColoredString("OK", "green");
        echo "\n\n";

        Setup::updateMetadata();


        if ($config['useSmartButton']) {
            // overwrite template file
            echo $colors->getColoredString("\nWrite smart-button template... ", "white");
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
            shell_exec("mkdir " . $config['installDir'] .
                    "/vendor/simplesamlphp/simplesamlphp/www/js");
            file_put_contents($config['installDir'] .
                    "/vendor/simplesamlphp/simplesamlphp/www/js/agid-spid-enter.js", $customized);
            echo $colors->getColoredString("OK", "green");

            // copy smart button css and img
            echo $colors->getColoredString("\nCopy smart-button resurces... ", "white");
            shell_exec("cp -rf " . $config['installDir'] . "/vendor/italia/spid-smart-button/css " .
                    $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/css");
            shell_exec("cp -rf " . $config['installDir'] . "/vendor/italia/spid-smart-button/img " .
                    $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/img");
            echo $colors->getColoredString("OK", "green");
        } else {
            // overwrite template file
            echo $colors->getColoredString("\nWrite spid button template... ", "white");
            shell_exec("mkdir " . $config['installDir'] .
                    "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            shell_exec("mkdir " . $config['installDir'] .
                    "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button/css");
            shell_exec("mkdir " . $config['installDir'] .
                    "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button/img");
            shell_exec("mkdir " . $config['installDir'] .
                    "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button/js");
            shell_exec("cp -rf " . $config['installDir'] .
                    "/vendor/italia/spid-sp-access-button/src/production/css " .
                    $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            shell_exec("cp -rf " . $config['installDir'] .
                    "/vendor/italia/spid-sp-access-button/src/production/img " .
                    $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            shell_exec("cp -rf " . $config['installDir'] .
                    "/vendor/italia/spid-sp-access-button/src/production/js " .
                    $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/www/spid-sp-access-button");
            echo $colors->getColoredString("OK", "green");
        }


        // apply simplesalphp patch for spid compliance
        // needed only for templates
        shell_exec("cp -rf " . $config['installDir'] . "/setup/simplesamlphp/simplesamlphp/templates/* " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/templates");

        // write example files
        if ($config['addExamples']) {
            echo $colors->getColoredString("\nWrite example files to www (login-spid.php)... ", "white");
            $vars = array("{{SDKHOME}}" => $config['installDir']);
            $template = file_get_contents($config['installDir'] . '/setup/sdk/login-spid.tpl', true);
            $customized = str_replace(array_keys($vars), $vars, $template);
            file_put_contents($config['wwwDir'] . "/login-spid.php", $customized);
            echo $colors->getColoredString("OK", "green");
        }

        // reset permissions
        echo $colors->getColoredString("\nSetting directories and files permissions... ", "white");
        shell_exec("find " . $config['installDir'] . "/. -type d -exec chmod 0755 {} \;");
        shell_exec("find " . $config['installDir'] . "/. -type f -exec chmod 0644 {} \;");
        shell_exec("chmod 777 " . $config['installDir'] . "/vendor/simplesamlphp/simplesamlphp/log");

        if ($config['addExamples']) {
            shell_exec("chmod 0644 " . $config['wwwDir'] . "/login-spid.php");
        }
        echo $colors->getColoredString("OK", "green");



        echo $colors->getColoredString("\n\nSPID PHP SDK successfully installed! Enjoy the identities\n\n", "green");
    }

    public static function updateMetadata() {
        $colors = new Colors();
        $_installDir = getcwd();
        $config = file_exists("spid-php-setup.json") ?
                json_decode(file_get_contents("spid-php-setup.json"), true) : array();
        
        $arrContextOptions = array("ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => true,
        ));

        $xml = file_get_contents('https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml', false, stream_context_create($arrContextOptions));

        if (!file_exists($config['installDir'] . "/vendor")) {
            echo "\nspid-php is not installed. Please install it first.\n\n composer install\n\n";
            exit(1);
        }

        // customize and copy metadata file
        $template = file_get_contents($config['installDir'] . '/setup/metadata/saml20-idp-remote.tpl', true);

        // setup IDP configurations
        $IDPMetadata = "";
        $IDPEntities = "";

        // add configuration for public test IDP
        if ($config['addTestIDP']) {
            echo $colors->getColoredString("\nWrite metadata for public test IDP... ", "white");
            $vars = array("{{ENTITYID}}" => "'" . $config['entityID'] . "'");
            $template_idp_test = file_get_contents($_installDir . '/setup/metadata/saml20-idp-remote-test.ptpl', true);
            $template_idp_test = str_replace(array_keys($vars), $vars, $template_idp_test);
            $IDPMetadata .= "\n\n" . $template_idp_test;
            $IDPEntities .= "\n\t\t\t\$this->idps['TEST'] = 'https://idptest.spid.gov.it';";
            echo $colors->getColoredString("OK", "green");
        }

        // add configuration for AgID Validator
        if ($config['addValidatorIDP']) {
            echo $colors->getColoredString("\nWrite metadata for AgID Validator... ", "white");
            $vars = array("{{ENTITYID}}" => "'" . $config['entityID'] . "'");
            $template_idp_validator = file_get_contents($_installDir . '/setup/metadata/saml20-idp-remote-validator.ptpl', true);
            $template_idp_validator = str_replace(array_keys($vars), $vars, $template_idp_validator);
            $IDPMetadata .= "\n\n" . $template_idp_validator;
            $IDPEntities .= "\n\t\t\t\$this->idps['VALIDATOR'] = 'https://validator.spid.gov.it';";
            echo $colors->getColoredString("OK", "green");
        }

        // retrieve IDP metadata
        echo $colors->getColoredString("\nRetrieve configurations for production IDPs... ", "white");

        // remove tag prefixes
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $xml);
        $xml = simplexml_load_string($xml);

        // add configuration for local test IDP
        if ($config['addLocalTestIDP'] != "") {
            echo $colors->getColoredString("\nRetrieve configuration for local test IDP... ", "white");
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $xml1 = file_get_contents($config['addLocalTestIDP'], false, stream_context_create($arrContextOptions));
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

        echo $colors->getColoredString("OK", "green");

        echo $colors->getColoredString("\nWrite metadata for production IDPs... ", "white");
        $vars = array("{{IDPMETADATA}}" => $IDPMetadata);
        $template = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] .
                "/vendor/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php", $template);
        echo $colors->getColoredString("OK", "green");

        // write sdk
        echo $colors->getColoredString("\nWrite sdk helper class... ", "white");
        $vars = array("{{SERVICENAME}}" => $config['serviceName'], "{{IDPS}}" => $IDPEntities);
        $template = file_get_contents($config['installDir'] . '/setup/sdk/spid-php.tpl', true);
        $customized = str_replace(array_keys($vars), $vars, $template);
        file_put_contents($config['installDir'] . "/spid-php.php", $customized);
        echo $colors->getColoredString("OK", "green");
        echo "\n\n";
    }

    public static function remove() {
        $colors = new Colors();
        $config = file_exists("spid-php-setup.json") ?
                json_decode(file_get_contents("spid-php-setup.json"), true) : array();

        // retrieve path and inputs
        $_installDir = getcwd();
        $_wwwDir = shell_exec('echo -n "$HOME/public_html"');
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
            echo "Please insert name for service endpoint (" .
            $colors->getColoredString($_serviceName, "green") . "): ";
            $serviceName = readline();
            if ($serviceName == null || $serviceName == "") {
                $serviceName = $_serviceName;
            }
        }

        echo $colors->getColoredString("\nRemove vendor directory [" .
                $installDir . "]... ", "white");
        shell_exec("rm -Rf " . $installDir . "/vendor");
        echo $colors->getColoredString("OK", "green");
        //echo $colors->getColoredString("\nRemove cert directory [" . $installDir . "/cert]... ", "white");
        //shell_exec("rm -Rf " . $installDir . "/cert");
        //echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove simplesamlphp service symlink [" .
                $wwwDir . "/" . $serviceName . "]... ", "white");
        shell_exec("rm " . $wwwDir . "/'" . $serviceName."'");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove sdk file [" .
                $installDir . "/spid-php.php]... ", "white");
        shell_exec("rm " . $installDir . "/spid-php.php");
        echo $colors->getColoredString("OK", "green");
        echo $colors->getColoredString("\nRemove composer lock file... ", "white");
        shell_exec("rm " . $installDir . "/composer.lock");
        echo $colors->getColoredString("OK", "green");


        echo $colors->getColoredString("\n\nSPID PHP SDK successfully removed\n\n", "green");
    }

}
