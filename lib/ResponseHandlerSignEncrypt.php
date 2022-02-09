<?php

class ResponseHandlerSignEncrypt extends ResponseHandler {

    public function sendResponse($redirect_uri, $data, $state) {
        echo "<form name='spidauth' action='".$redirect_uri."' method='POST'>";

        $exp_time = $this->config['tokenExpTime'] ?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $iss = $this->issuer;
        $aud = $redirect_uri;
        $jwk_pem = TOKEN_PRIVATE_KEY;

        $data = $this->makeJWS($data, $exp_time, $iss, $aud, $jwk_pem);

        $secret = $this->config['clients'][$client_id]['client_secret'];
        $encryptedDataToken = $this->makeJWE($data, $exp_time, $iss, $aud, $secret);

        echo "<input type='hidden' name='data' value='".$encryptedDataToken."' />";

        echo "<input type='hidden' name='state' value='".$state."' />";
        echo "</form>";
        echo "<script type='text/javascript'>";
        echo "  document.spidauth.submit();";
        echo "</script>";
    }
}