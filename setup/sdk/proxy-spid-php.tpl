<?php

require_once("spid-php.php");

/**
 * Class PROXY_SPID_PHP
 * It extends the SPID_PHP to utilize SDK as proxy.
 * URL to call proxy login is formed as follow:
   /proxy-spid.php?client_id=<client_id>&action=login&redirect_uri=<redirect_uri>&idp=<idp>&state=<state>
 * <client_id> and <redirect_uri> are configured during setup
 */
class PROXY_SPID_PHP extends SPID_PHP {

    public function __construct($client_id, $redirect_uri) {
        parent::__construct();
        $this->client_id = $client_id;
        $this->redirect_uri = urlencode($redirect_uri);
    }

    public function addSPIDButtonListItems(): string{
        $button_li = "
                <li class=\"spid-idp-button-link\" data-idp=\"arubaid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=ArubaPEC S.p.A.&state=state\"><span class=\"spid-sr-only\">Aruba ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-arubaid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-arubaid.png'; this.onerror=null;\" alt=\"Aruba ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"infocertid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=InfoCert S.p.A.&state=state\"><span class=\"spid-sr-only\">Infocert ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-infocertid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-infocertid.png'; this.onerror=null;\" alt=\"Infocert ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"intesaid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=IN.TE.S.A. S.p.A.&state=state\"><span class=\"spid-sr-only\">Intesa ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-intesaid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-intesaid.png'; this.onerror=null;\" alt=\"Intesa ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"lepidaid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=Lepida S.p.A.&state=state\"><span class=\"spid-sr-only\">Lepida ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-lepidaid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-lepidaid.png'; this.onerror=null;\" alt=\"Lepida ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"namirialid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=Namirial&state=state\"><span class=\"spid-sr-only\">Namirial ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-namirialid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-namirialid.png'; this.onerror=null;\" alt=\"Namirial ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"posteid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=Poste Italiane SpA&state=state\"><span class=\"spid-sr-only\">Poste ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-posteid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-posteid.png'; this.onerror=null;\" alt=\"Poste ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"sielteid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=Sielte S.p.A.&state=state\"><span class=\"spid-sr-only\">Sielte ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-sielteid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-sielteid.png'; this.onerror=null;\" alt=\"Sielte ID\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"spiditalia\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=Register.it S.p.A.&state=state\"><span class=\"spid-sr-only\">SPIDItalia Register.it</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-spiditalia.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-spiditalia.png'; this.onerror=null;\" alt=\"SpidItalia\" /></a>
                </li>
                <li class=\"spid-idp-button-link\" data-idp=\"timid\">
                    <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=TI Trust Technologies srl&state=state\"><span class=\"spid-sr-only\">Tim ID</span><img src=\"/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-timid.svg\" onerror=\"this.src='/{{SERVICENAME}}/spid-sp-access-button/img/spid-idp-timid.png'; this.onerror=null;\" alt=\"Tim ID\" /></a>
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


        $button_local = "";
        if ($this->isIdPAvailable("LOCAL")) {
            $button_local = "
                    <li class=\"spid-idp-button-link\" data-idp=\"localid\">
                        <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=LOCAL&state=state\">IDP LOCAL</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("VALIDATOR")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=VALIDATOR&state=state\">SPID Validator</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("DEMO")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=DEMO&state=state\">SPID DEMO</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("DEMOVALIDATOR")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=DEMOVALIDATOR&state=state\">SPID Demo (Validator mode)</a>
                    </li>
                ";
        }

        if ($this->isIdPAvailable("TEST")) {
            $button_li .= "
                    <li class=\"spid-idp-support-link\">
                        <a href=\"/proxy-spid.php?client_id=".$this->client_id."&action=login&redirect_uri=".$this->redirect_uri."&idp=TEST&state=state\">SPID Test</a>
                    </li>
                ";
        }

        return $button_li;
    }

}

?>
