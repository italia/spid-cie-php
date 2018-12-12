<?php

    require_once("vendor/simplesamlphp/simplesamlphp/lib/_autoload.php");

    class SPID_PHP {
        private $spid_auth;
        private $idps = array();

        function __construct() {
            $this->spid_auth = new SimpleSAML_Auth_Simple('service');
            {{IDPS}}
        }

        public function requireAuth() {
            $this->spid_auth->requireAuth();
        }
    
        public function login($idp, $l) {
            $l = ($l=="2" || $l=="3")? $l : "1";
            $spidlevel = "https://www.spid.gov.it/SpidL" . $l;

            $this->spid_auth->login(array(
                'saml:AuthnContextClassRef' => $spidlevel,
                'saml:AuthnContextComparison' => 'SAML2\Constants::COMPARISON_EXACT',
                'saml:idp' => $this->idps[$idp],
                'saml:NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
                //'ErrorURL' => '/error_handler.php'
            ));
        }

        public function logout() {
            $this->spid_auth->logout();
        }
    
        public function getLogoutURL($returnTo = null) {
            return $this->spid_auth->getLogoutURL($returnTo);
        }
            
        public function getAttributes() {
            return $this->spid_auth->getAttributes();
        }

        public function getAttribute($attribute) {
            $attributes = $this->spid_auth->getAttributes();
            return $attributes[$attribute];
        }

        public function isAuthenticated() {
            return $this->spid_auth->isAuthenticated();
        }

        public function insertSPIDButtonCSS() {
            echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"{{SERVICENAME}}/spid-sp-access-button/css/spid-sp-access-button.min.css\" />";
        }

        public function insertSPIDButtonJS() {
            echo "<script type=\"text/javascript\" src=\"{{SERVICENAME}}/spid-sp-access-button/js/jquery.min.js\"></script>";
            echo "<script type=\"text/javascript\" src=\"{{SERVICENAME}}/spid-sp-access-button/js/spid-sp-access-button.min.js\"></script>";
            echo "
                <script>
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-small-root-get\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-medium-root-get\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-large-root-get\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-xlarge-root-get\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-small-root-post\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-medium-root-post\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-large-root-post\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
            
                    $(document).ready(function(){
                        var rootList = $(\"#spid-idp-list-xlarge-root-post\");
                        var idpList = rootList.children(\".spid-idp-button-link\");
                        var lnkList = rootList.children(\".spid-idp-support-link\");
                        while (idpList.length) {
                            rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
                        }
                        rootList.append(lnkList);
                    });
                </script>            
            ";            
        }

        public function insertSPIDButton($size) {
            $button_test = "";
            if(array_key_exists('TEST', $this->idps)) {
                $button_test = "
                    <li class=\"spid-idp-button-link\" data-idp=\"testid\">
                        <a href=\"?idp=TEST\">IDP TEST</a>
                    </li>
                ";
            }
            $button_validator = "";
            if(array_key_exists('VALIDATOR', $this->idps)) {
                $button_validator = "
                    <li class=\"spid-idp-button-link\" data-idp=\"testid\">
                        <a href=\"?idp=VALIDATOR\">AgID VALIDATOR</a>
                    </li>
                ";
            }            
            $button_li = 
                $button_test.
                $button_validator."
                <li class=\"spid-idp-button-link\" data-idp=\"arubaid\">
                    <a href=\"?idp=ArubaPEC S.p.A.\"><span class=\"spid-sr-only\">Aruba ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-arubaid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-arubaid.png'; this.onerror=null;\" alt=\"Aruba ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"infocertid\">
                    <a href=\"?idp=InfoCert S.p.A.\"><span class=\"spid-sr-only\">Infocert ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-infocertid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-infocertid.png'; this.onerror=null;\" alt=\"Infocert ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"intesaid\">
                    <a href=\"?idp=IN.TE.S.A. S.p.A.\"><span class=\"spid-sr-only\">Intesa ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-intesaid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-intesaid.png'; this.onerror=null;\" alt=\"Intesa ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"lepidaid\">
                    <a href=\"?idp=Lepida S.p.A.\"><span class=\"spid-sr-only\">Lepida ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-lepidaid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-lepidaid.png'; this.onerror=null;\" alt=\"Lepida ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"namirialid\">
                    <a href=\"?idp=Namirial\"><span class=\"spid-sr-only\">Namirial ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-namirialid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-namirialid.png'; this.onerror=null;\" alt=\"Namirial ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"posteid\">
                    <a href=\"?idp=Poste Italiane SpA\"><span class=\"spid-sr-only\">Poste ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-posteid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-posteid.png'; this.onerror=null;\" alt=\"Poste ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"sielteid\">
                    <a href=\"?idp=Sielte S.p.A.\"><span class=\"spid-sr-only\">Sielte ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-sielteid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-sielteid.png'; this.onerror=null;\" alt=\"Sielte ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"spiditalia\">
                    <a href=\"?idp=Register.it S.p.A.\"><span class=\"spid-sr-only\">SPIDItalia Register.it</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-spiditalia.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-spiditalia.png'; this.onerror=null;\" alt=\"SpidItalia\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"timid\">
                    <a href=\"?idp=TI Trust Technologies srl\"><span class=\"spid-sr-only\">Tim ID</span><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-timid.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-timid.png'; this.onerror=null;\" alt=\"Tim ID\" /></a>
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
            switch($size) {
                case "S":
                    $button = "
                        <!-- AGID - SPID IDP BUTTON SMALL \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-s button-spid\" spid-idp-button=\"#spid-idp-button-small-get\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
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
                            <span class=\"italia-it-button-icon\"><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
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
                            <span class=\"italia-it-button-icon\"><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
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
                            <span class=\"italia-it-button-icon\"><img src=\"{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
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

        public function insertSPIDSmartButton($size) {
            $url = $this->spid_auth->getLoginURL();
          
            echo "
                <link rel='stylesheet' href='/{{SERVICENAME}}/css/agid-spid-enter.css'>
                <div class='agid-spid-enter-button agid-spid-enter-button-size-".strtolower($size)."'>
                    <button class='agid-spid-enter agid-spid-enter-size-".strtolower($size)."' onclick=\"location.href='".$url."'\">
                        <span class='agid-spid-enter-icon'>
                            <img aria-hidden='true' src='/{{SERVICENAME}}/img/spid-ico-circle-bb.svg' alt='Entra con SPID' />
                        </span>
                        <span class='agid-spid-enter-text'>Entra con SPID</span>
                    </button>
                </div>
            ";       
        }
    }

?>