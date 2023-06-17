<?php

    require_once("vendor/simplesamlphp/simplesamlphp/lib/_autoload.php");
    require_once("setup/sdk/AbstractSPID.php");

    class SPID_PHP extends \SPID\AbtractSPID {
        private $spid_auth;
        private $idps = array();
        private $purpose = null;
        private $service = 'spid';
        
        public const SPID_ENABLED = {{SPID_ENABLED}};
        public const CIE_ENABLED = {{CIE_ENABLED}};

        function __construct($production=false, $service='spid') {
            $this->spid_auth = new SimpleSAML\Auth\Simple($service);
            $this->production = $production;
            $this->service = $service;

            {{IDPS}}
        }

	public function isSPIDEnabled() {
	    return self::SPID_ENABLED;
	}

	public function isCIEEnabled() {
	    return self::CIE_ENABLED;
	}
	
        public function getIdP() {
            return $this->spid_auth->getAuthData('saml:sp:IdP');
        }

        public function isCIE() {
            $idp = $this->getIdP();
            return ($idp==$this->idps['CIE TEST'] || $idp==$this->idps['CIE']);
        }

        public function isCIEKey($key) {
            return ($key=="CIE TEST" || $key=="CIE");
        }

        public function getIdPKey() {
            $idp = $this->getIdP();
            foreach($this->idps as $k=>$v) {
                if($v==$idp) return $k;
            }
        }

        public function getAuthDataArray() {
            return $this->spid_auth->getAuthDataArray();
        }

        public function getResponseID() {
            return $this->spid_auth->getAuthDataArray()['saml:sp:prevAuth']['id'];
        }

        public function isIdPAvailable($idp) {
            $available = false;
            if($this->production && (
                $idp == 'DEMO'
                || $idp == 'DEMOVALIDATOR'
                || $idp == 'VALIDATOR'
                || $idp == 'LOCAL'
            )) {
                $available = false;
            } else {
                $available = (isset($this->idps[$idp]) && $this->idps[$idp]!=null);
            }
            
            return $available;
        }

        public function isIdP($idp) {
            return (
                $idp!=null
                && $this->getIdp()!=null
                && isset($this->idps[$idp])
                && ($this->idps[$idp]==$this->getIdp())
            );
        }

        public function setProfessionalUse($professionalUse) {
            if($professionalUse===true) {
                $this->purpose = "P";
            }
        }

        public function setPurpose($p) {
            $this->purpose = $p;
        }

        public function requireAuth() {
            $this->spid_auth->requireAuth();
        }
    
        public function login($idp, $l, $returnTo="", $attributeIndex=null, $post=false) {
            // common for SPID & CIE
            $comparison = \SAML2\Constants::COMPARISON_MINIMUM;

            // override for CIE
            $isCIEIdP = $this->isCIEKey($idp);
            if($isCIEIdP) {
                $l = ($l=="1" || $l=="2")? $l : "3";
                $post = true;

                /*
                * Decreto 8 settembre 2022 “Modalità di impiego della carta di identità elettronica” art. 4
                * consente l'utilizzo di CIE a livello 1 e 2
                * impostato di default a 3 se non specificato
                */
                //$comparison = $isCIEIdP? \SAML2\Constants::COMPARISON_EXACT : \SAML2\Constants::COMPARISON_MINIMUM;

            } else {
                $l = ($l=="1" || $l=="3")? $l : "2";
            }

            $spidcie_level = "https://www.spid.gov.it/SpidL" . $l;
            $binding = $post? \SAML2\Constants::BINDING_HTTP_POST : \SAML2\Constants::BINDING_HTTP_REDIRECT;

            $config = array(
                'saml:AuthnContextClassRef' => $spidcie_level,
                'saml:AuthnContextComparison' => $comparison,
                'saml:idp' => $this->idps[$idp],
                'saml:NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
                'saml:AttributeConsumingServiceIndex' => $attributeIndex,
                'saml:SingleSignOnServiceProtocolBinding' => $binding
                //'ErrorURL' => '/error_handler.php'
            );

            if($this->purpose!=null
                && in_array($this->purpose, array('P', 'LP', 'PG', 'PF', 'PX'))
            ) {
                $dom = \SAML2\DOMDocumentFactory::create();
                $elem = $dom->createElementNS('https://spid.gov.it/saml-extensions', 'spid:Purpose', $this->purpose);
                $pExt[] = new \SAML2\XML\Chunk($elem);  
                $config['saml:Extensions'] = $pExt;
            } 
            
            if(!empty($returnTo)) $config['ReturnTo'] = $returnTo;
            
            $this->spid_auth->login($config);
        }

        public function logout($returnTo = null, $saml_logout = true) {
            if($saml_logout) {
                $this->spid_auth->logout($returnTo);
            } else {
               $session = SimpleSAML\Session::getSessionFromRequest();
                if ($session->isValid($this->service)) {
                    $session->doLogout($this->service);
                }
            }
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
            echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"/{{SERVICENAME}}/spid-sp-access-button/css/spid-sp-access-button.min.css\" />";
        }

        public function insertSPIDButtonJS() {
            echo "<script type=\"text/javascript\" src=\"/{{SERVICENAME}}/spid-sp-access-button/js/jquery.min.js\"></script>";
            echo "<script type=\"text/javascript\" src=\"/{{SERVICENAME}}/spid-sp-access-button/js/spid-sp-access-button.min.js\"></script>";
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

        public function insertSPIDButton($size, $method='GET') {
			$size = strtoupper($size);
			$method = strtolower($method);

            $button_li = $this->addSPIDButtonListItems($method);

            switch($size) {
                case "S":
                    $button = "
                        <!-- AGID - SPID IDP BUTTON SMALL \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-s button-spid\" spid-idp-button=\"#spid-idp-button-small-".$method."\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
                        </a>
                        <div id=\"spid-idp-button-small-".$method."\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-small-root-".$method."\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON SMALL \"ENTRA CON SPID\" * end * -->
                    ";
                break;

                case "M":
                    $button = "
                        <!-- AGID - SPID IDP BUTTON MEDIUM \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-m button-spid\" spid-idp-button=\"#spid-idp-button-medium-".$method."\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
                        </a>
                        <div id=\"spid-idp-button-medium-".$method."\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-medium-root-".$method."\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON MEDIUM \"ENTRA CON SPID\" * end * -->
                    ";
                break;

                case "L":
                    $button = "
                        <!-- AGID - SPID IDP BUTTON LARGE \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-l button-spid\" spid-idp-button=\"#spid-idp-button-large-".$method."\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
                        </a>
                        <div id=\"spid-idp-button-large-".$method."\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-large-root-".$method."\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON LARGE \"ENTRA CON SPID\" * end * -->
                    ";
                break;

                case "XL";
                    $button = "
                        <!-- AGID - SPID IDP BUTTON XLARGE \"ENTRA CON SPID\" * begin * -->
                        <a href=\"#\" class=\"italia-it-button italia-it-button-size-xl button-spid\" spid-idp-button=\"#spid-idp-button-xlarge-".$method."\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            <span class=\"italia-it-button-icon\"><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-ico-circle-bb.png'; this.onerror=null;\" alt=\"\" /></span>
                            <span class=\"italia-it-button-text\">Entra con SPID</span>
                        </a>
                        <div id=\"spid-idp-button-xlarge-".$method."\" class=\"spid-idp-button spid-idp-button-tip spid-idp-button-relative\">
                            <ul id=\"spid-idp-list-xlarge-root-".$method."\" class=\"spid-idp-button-menu\" aria-labelledby=\"spid-idp\">
                                ".$button_li."
                            </ul>
                        </div>
                        <!-- AGID - SPID IDP BUTTON XLARGE \"ENTRA CON SPID\" * end * -->
                    ";
                break;
            }

            if($method=="post") {
                $button = "<form name=\"spid_idp_access\" action=\"#\" method=\"post\">" . $button . "</form>";
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

        public function addSPIDButtonListItems($method='GET'): string {
			$method = strtolower($method);

            $registry_idp_json = file_get_contents('https://registry.spid.gov.it/entities-idp?output=json');
            $registry_idp = json_decode($registry_idp_json, true);

            if($registry_idp!=null && is_array($registry_idp) && count($registry_idp)) {
                file_put_contents('spid-idps.json', $registry_idp_json);
            } else {
                $registry_idp = json_decode(file_get_contents('spid-idps.json'), true);
            }
            
            if($method=='post') {

                $button_li = "";
                foreach($registry_idp as $registry_idp_entity) {
                    $button_li .= "
                        <li class=\"spid-idp-button-link\" data-idp=\"" . $registry_idp_entity['organization_name'] . "\">
                            <button class=\"idp-button-idp-logo\" 
                                name=\"" . $registry_idp_entity['organization_name'] ."\" 
                                value=\"" . $registry_idp_entity['organization_name'] . "\" type=\"submit\">
                                <span class=\"spid-sr-only\">" . $registry_idp_entity['organization_name'] . "</span>
                                <img class=\"spid-idp-button-logo\" 
                                    src=\"" . $registry_idp_entity['logo_uri'] . "\" 
                                    onerror=\"this.src='" . $registry_idp_entity['logo_uri'] . "'; this.onerror=null;\" 
                                    alt=\"" . $registry_idp_entity['organization_name'] . "\" />
                            </button>
                        </li>
                    ";
                }

				$button_li .= "
					<li class=\"spid-idp-support-link\" data-spidlink=\"info\">
						<a href=\"https://www.spid.gov.it\">Maggiori informazioni</a>
					</li>
					<li class=\"spid-idp-support-link\" data-spidlink=\"rich\">
						<a href=\"https://www.spid.gov.it/richiedi-spid\">Non hai SPID?</a>
					</li>
					<li class=\"spid-idp-support-link\" data-spidlink=\"help\">
						<a href=\"https://www.spid.gov.it/serve-aiuto\">Serve aiuto?</a>
					</li>
				";
				if(!$this->production) {

					if (array_key_exists('LOCAL', $this->idps)) {
							$button_li .= "
									<li class=\"spid-idp-button-link\" data-idp=\"localid\">
											<button class=\"idp-button-idp-logo\" name=\"idp\" value=\"LOCAL\" type=\"submit\"><span class=\"spid-sr-only\">IDP LOCAL</span>IDP LOCAL</button>
									</li>
							";
					}

					if (array_key_exists('VALIDATOR', $this->idps)) {
							$button_li .= "
									<li class=\"spid-idp-support-link\">
											<button class=\"idp-button-idp-logo\" name=\"idp\" value=\"VALIDATOR\" type=\"submit\"><span class=\"spid-sr-only\">SPID Validator</span>SPID Validator</button>
									</li>
							";
					}

					if (array_key_exists('DEMO', $this->idps)) {
							$button_li .= "
									<li class=\"spid-idp-support-link\">
											<button class=\"idp-button-idp-logo\" name=\"idp\" value=\"DEMO\" type=\"submit\"><span class=\"spid-sr-only\">SPID Demo</span>SPID Demo</button>
									</li>
							";
					}

					if (array_key_exists('DEMOVALIDATOR', $this->idps)) {
							$button_li .= "
									<li class=\"spid-idp-support-link\">
											<button class=\"idp-button-idp-logo\" name=\"idp\" value=\"DEMOVALIDATOR\" type=\"submit\"><span class=\"spid-sr-only\">SPID Demo (Validator mode)</span>SPID Demo (Validator mode)</button>
									</li>
							";
					}

					if (array_key_exists('TEST', $this->idps)) {
							$button_li .= "
									<li class=\"spid-idp-support-link\">
											<button class=\"idp-button-idp-logo\" name=\"idp\" value=\"TEST\" type=\"submit\"><span class=\"spid-sr-only\">SPID Test</span>SPID Test</button>
									</li>
							";
					}
				}
			} else {

                $button_li = "";
                foreach($registry_idp as $registry_idp_entity) {
                    $button_li .= "
                        <li class=\"spid-idp-button-link\" data-idp=\"" . $registry_idp_entity['organization_name'] . "\">
                            <a href=\"?idp=" . $registry_idp_entity['organization_name'] . "\">
                                <span class=\"spid-sr-only\">" . $registry_idp_entity['organization_name'] . "</span>
                                <img src=\"" . $registry_idp_entity['logo_uri'] . "\" 
                                    onerror=\"this.src='" . $registry_idp_entity['logo_uri'] . "\" 
                                    alt=\"" . $registry_idp_entity['organization_name'] . "\" />
                            </a>
                        </li>
                    ";
                }

				$button_li .= "
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

				if(!$this->production) {

					if (array_key_exists('LOCAL', $this->idps)) {
						$button_li .= "
							<li class=\"spid-idp-button-link\" data-idp=\"localid\">
								<a href=\"?idp=LOCAL\">IDP LOCAL</a>
							</li>
						";
					}

					if (array_key_exists('VALIDATOR', $this->idps)) {
						$button_li .= "
							<li class=\"spid-idp-support-link\">
								<a href=\"?idp=VALIDATOR\">SPID Validator</a>
							</li>
						";
					}

					if (array_key_exists('DEMO', $this->idps)) {
						$button_li .= "
							<li class=\"spid-idp-support-link\">
								<a href=\"?idp=DEMO\">SPID Demo</a>
							</li>
						";
					}

					if (array_key_exists('DEMOVALIDATOR', $this->idps)) {
						$button_li .= "
							<li class=\"spid-idp-support-link\">
								<a href=\"?idp=DEMOVALIDATOR\">SPID Demo (Validator mode)</a>
							</li>
						";
					}

					if (array_key_exists('TEST', $this->idps)) {
						$button_li .= "
							<li class=\"spid-idp-support-link\">
								<a href=\"?idp=TEST\">SPID Test</a>
							</li>
						";
					}
				}
			}

            return $button_li;
        }
        
        
        public function insertCIEButton($size='default') {
            echo "
                <div class=\"cie-button\" style=\"width: 280px;\">
                    <a class=\"cie-button\" role=\"button\"
                        href=\"?idp=CIE TEST\" >
                        <span class=\"cie-button-icon\">
                            <img aria-hidden=\"true\" src=\"/{{SERVICENAME}}/cie-graphics/SVG/entra_con_cie.svg\" alt=\"Entra con CIE\" />
                        </span>
                        <span class=\"sr-only\" style=\"display:none\">Entra con CIE</span>
                    </a>
                </div>
            ";    
        }
    }

?>
