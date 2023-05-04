<?php

class ResponseHandlerSignEncrypt extends ResponseHandler {

    public function sendResponse($redirect_uri, $data, $state) {
        echo "<form name=\"spidauth\" action=\"".$redirect_uri."\" method=\"POST\">";

        $exp_time = $this->config['tokenExpTime'] ?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $iss = $this->issuer;
        $aud = $redirect_uri;
        $jwk_pem = TOKEN_PRIVATE_KEY;

        $data = $this->makeJWS($data, $exp_time, $iss, $aud, $jwk_pem);

        $secret = $this->config['client_secret'];
        $encryptedDataToken = $this->makeJWE($data, $exp_time, $iss, $aud, $secret);

        echo "<input type=\"hidden\" name=\"data\" value=\"".$encryptedDataToken."\" />";

        echo "<input type=\"hidden\" name=\"state\" value=\"".$state."\" />";
        echo "<input type=\"hidden\" name=\"providerId\" value=\"".$this->providerId."\" />";
        echo "<input type=\"hidden\" name=\"providerName\" value=\"".$this->providerName."\" />";
        echo "<input type=\"hidden\" name=\"responseId\" value=\"".$this->responseId."\" />";
        echo "</form>";
        echo "<script type=\"text/javascript\">";
        echo "  document.spidauth.submit();";
        echo "</script>";
    }
}