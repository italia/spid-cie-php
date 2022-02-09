<?php

class ResponseHandlerSign extends ResponseHandler {

    public function sendResponse($redirect_uri, $data, $state) {
        echo "<form name='spidauth' action='".$redirect_uri."' method='POST'>";

        $exp_time = $this->config['tokenExpTime'] ?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $iss = $this->issuer;
        $aud = $redirect_uri;
        $jwk_pem = TOKEN_PRIVATE_KEY;

        $signedDataToken = $this->makeJWS($data, $exp_time, $iss, $aud, $jwk_pem);
        echo "<input type='hidden' name='data' value='".$signedDataToken."' />";

        echo "<input type='hidden' name='state' value='".$state."' />";
        echo "</form>";
        echo "<script type='text/javascript'>";
        echo "  document.spidauth.submit();";
        echo "</script>";
    }
}