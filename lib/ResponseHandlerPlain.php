<?php

class ResponseHandlerPlain extends ResponseHandler {

    public function sendResponse($redirect_uri, $data, $state) {
        echo "<form name='spidauth' action='".$redirect_uri."' method='POST'>";
        foreach($data as $attribute=>$value) {
            echo "<input type='hidden' name='".$attribute."' value='".$value."' />";
        }
        echo "<input type='hidden' name='state' value='".$state."' />";
        echo "</form>";
        echo "<script type='text/javascript'>";
        echo "  document.spidauth.submit();";
        echo "</script>";
    }
}