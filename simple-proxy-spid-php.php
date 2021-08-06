<?php

require_once("vendor/simplesamlphp/simplesamlphp/lib/_autoload.php");
require_once("spid-php.php");

class PROXY_SPID_PHP extends SPID_PHP {
    public function __construct() {
        parent::__construct();
    }

    public function insertSPIDButton($size) {
        $button_li = "
                <li class=\"spid-idp-button-link\" data-idp=\"arubaid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=ArubaPEC S.p.A.&state=state\"><span class=\"spid-sr-only\">Aruba ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-arubaid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-arubaid.png'; this.onerror=null;\" alt=\"Aruba ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"infocertid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=InfoCert S.p.A.&state=state\"><span class=\"spid-sr-only\">Infocert ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-infocertid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-infocertid.png'; this.onerror=null;\" alt=\"Infocert ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"intesaid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=IN.TE.S.A. S.p.A.&state=state\"><span class=\"spid-sr-only\">Intesa ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-intesaid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-intesaid.png'; this.onerror=null;\" alt=\"Intesa ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"lepidaid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=Lepida S.p.A.&state=state\"><span class=\"spid-sr-only\">Lepida ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-lepidaid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-lepidaid.png'; this.onerror=null;\" alt=\"Lepida ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"namirialid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=Namirial&state=state\"><span class=\"spid-sr-only\">Namirial ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-namirialid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-namirialid.png'; this.onerror=null;\" alt=\"Namirial ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"posteid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=Poste Italiane SpA&state=state\"><span class=\"spid-sr-only\">Poste ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-posteid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-posteid.png'; this.onerror=null;\" alt=\"Poste ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"sielteid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=Sielte S.p.A.&state=state\"><span class=\"spid-sr-only\">Sielte ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-sielteid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-sielteid.png'; this.onerror=null;\" alt=\"Sielte ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"spiditalia\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=Register.it S.p.A.&state=state\"><span class=\"spid-sr-only\">SPIDItalia Register.it</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-spiditalia.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-spiditalia.png'; this.onerror=null;\" alt=\"SpidItalia\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"timid\">
                    <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=TI Trust Technologies srl&state=state\"><span class=\"spid-sr-only\">Tim ID</span><img src=\"/spid-php/spid-sp-access-button/img/spid-idp-timid.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-idp-timid.png'; this.onerror=null;\" alt=\"Tim ID\" /></a>
                </li>
                <li class=\"spid-idp-support-link\">
                    <a href=\"https://www.spid.gov.it\">Maggiori informazioni</a>
                </li>
                <li class=\"spid-idp-support-link\">
                    <a href=\"https://www.spid.gov.it/richiedi-spid\">Non hai SPID?</a>
                </li>
                <li class=\"spid-idp-support-link\">
                    <a href=\"https://www.spid.gov.it/serve-aiuto\">Serve aiuto?</a>
                </li>
            ";


        $button_test = "";
        if(parent::isIdPAvailable("TEST")) {
            $button_test = "
                    <li class=\"spid-idp-button-link\" data-idp=\"testid\">
                        <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=TEST&state=state\">IDP TEST</a>
                    </li>
                ";
        }
        $button_local = "";
        if(parent::isIdPAvailable("LOCAL")) {
            $button_local = "
                    <li class=\"spid-idp-button-link\" data-idp=\"localid\">
                        <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=LOCAL&state=state\">IDP LOCAL</a>
                    </li>
                ";
        }

        if(parent::isIdPAvailable("VALIDATOR")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=VALIDATOR&state=state\">SPID Validator</a>
                    </li>
                ";
        }

        if(parent::isIdPAvailable("DEMO")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=DEMO&state=state\">SPID DEMO</a>
                    </li>
                ";
        }

        if(parent::isIdPAvailable("DEMOVALIDATOR")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=DEMOVALIDATOR&state=state\">SPID Demo (Validator mode)</a>
                    </li>
                ";
        }

        if(parent::isIdPAvailable("TEST")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=60f6ad4d6a9df&action=login&redirect_uri=http://localhost:8090/redirect.php&idp=TEST&state=state\">SPID Test</a>
                    </li>
                ";
        }


        $buttonTitle ="Entra con SPID";
        switch($size) {
            case "S":
                $button = "
                        <!-- AGID - SPID IDP BUTTON SMALL \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-s button-spid\" spid-idp-button=\"#spid-idp-button-small-get\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">".$buttonTitle."</span>
                        </a>
                        <div id=\"spid-idp-button-small-get\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-small-root-get\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON SMALL \"ENTRA CON SPID\" * end * -->
                    ";
                break;

            case "M":
                $button = "
                        <!-- AGID - SPID IDP BUTTON MEDIUM \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-m button-spid\" spid-idp-button=\"#spid-idp-button-medium-get\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">".$buttonTitle."</span>
                        </a>
                        <div id=\"spid-idp-button-medium-get\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-medium-root-get\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON MEDIUM \"ENTRA CON SPID\" * end * -->
                    ";
                break;

            case "L":
                $button = "
                        <!-- AGID - SPID IDP BUTTON LARGE \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-l button-spid\" spid-idp-button=\"#spid-idp-button-large-get\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">".$buttonTitle."</span>
                        </a>
                        <div id=\"spid-idp-button-large-get\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-large-root-get\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON LARGE \"ENTRA CON SPID\" * end * -->
                    ";
                break;

            case "XL";
                $button = "
                        <!-- AGID - SPID IDP BUTTON XLARGE \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-xl button-spid\" spid-idp-button=\"#spid-idp-button-xlarge-get\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/spid-php/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">".$buttonTitle."</span>
                        </a>
                        <div id=\"spid-idp-button-xlarge-get\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-xlarge-root-get\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON XLARGE \"ENTRA CON SPID\" * end * -->
                    ";
                break;
        }
        echo $button;
    }
}

?>
