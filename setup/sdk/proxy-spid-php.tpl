<?php

require_once("spid-php.php");

/**
 * Class PROXY_SPID_PHP
 * It extends the SPID_PHP to utilize SDK as proxy.
 * URL to call proxy login is formed as follow:
   /proxy.php?client_id=<client_id>&action=login&redirect_uri=<redirect_uri>&idp=<idp>&state=<state>
 * <client_id> and <redirect_uri> are configured during setup
 */
class PROXY_SPID_PHP extends SPID_PHP {

    public function __construct($client_id, $redirect_uri, $state='', $production=false, $servicename='spid') {
        parent::__construct($production, $servicename);
        $this->client_id = $client_id;
        $this->redirect_uri = urlencode($redirect_uri);
        $this->state = $state;
    }

    public function addSPIDButtonListItems($method='GET'): string{

        $registry_idp_json = file_get_contents('https://registry.spid.gov.it/entities-idp?output=json');
        $registry_idp = json_decode($registry_idp_json, true);

        if($registry_idp!=null && is_array($registry_idp) && count($registry_idp)) {
            file_put_contents('spid-idps.json', $registry_idp_json);
        } else {
            $registry_idp = json_decode(file_get_contents('spid-idps.json'), true);
        }
        
        $button_li = "";
        foreach($registry_idp as $registry_idp_entity) {
            $button_li .= "
                <li class=\"spid-idp-button-link\" data-idp=\"" . $registry_idp_entity['organization_name'] . "\">
                    <a href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=" . $registry_idp_entity['organization_name'] . "&state=".$this->state."\">
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


        $button_local = "";
        if ($this->isIdPAvailable("LOCAL")) {
            $button_local = "
                    <li class=\"spid-idp-button-link\" data-idp=\"localid\">
                        <a href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=LOCAL&state=".$this->state."\">IDP LOCAL</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("VALIDATOR")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=VALIDATOR&state=".$this->state."\">SPID Validator</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("DEMO")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=DEMO&state=".$this->state."\">SPID DEMO</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("DEMOVALIDATOR")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=DEMOVALIDATOR&state=".$this->state."\">SPID Demo (Validator mode)</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("TEST")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=TEST&state=".$this->state."\">SPID Test</a>
                    </li>
                ";
        }

        return $button_li;
    }


	public function insertCIEButton($size='default') {
	    echo "
		<div class=\"cie-button\"  style=\"width: 280px;\">
		    <a class=\"cie-button\" role=\"button\"
		        href=\"/proxy.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=CIE TEST&state=".$this->state."\">
		        <span class=\"cie-button-icon\">
		            <img aria-hidden=\"true\" src=\"/{{SERVICENAME}}/cie-graphics/SVG/entra_con_cie.svg\" alt=\"Entra con CIE\" alt=\"Entra con CIE\" />
		        </span>
		        <span class=\"sr-only\" style=\"display:none\">Entra con CIE</span>
		    </a>
		</div>
	    ";    
	}
}

?>
