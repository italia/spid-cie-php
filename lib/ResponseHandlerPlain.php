<?php

class ResponseHandlerPlain extends ResponseHandler {

    public function sendResponse($redirect_uri, $data, $state) {
        echo "<form name=\"spidauth\" action=\"".$redirect_uri."\" method=\"POST\">";
        foreach($data as $attribute=>$value) {
            echo "<input type=\"hidden\" name=\"".$attribute."\" value=\"".$value."\" />";
        }
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