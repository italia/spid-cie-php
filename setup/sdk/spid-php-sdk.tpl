<?php

    require_once("vendor/simplesamlphp/simplesamlphp/lib/_autoload.php");

    class SPID_PHP_SDK {
        private $spid_auth;

        function __construct($level) {
            $this->spid_auth = new SimpleSAML_Auth_Simple('service-l'.$level);
        }

        public function requireAuth() {
            $this->spid_auth->requireAuth();
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

        public function insertSPIDButton($size) {
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