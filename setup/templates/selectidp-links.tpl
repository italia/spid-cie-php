<?php

// customized 20180221

if (!array_key_exists('header', $this->data)) {
    $this->data['header'] = 'selectidp';
}
$this->data['header'] = $this->t($this->data['header']);
$this->data['autofocus'] = 'preferredidp';

foreach ($this->data['idplist'] as $idpentry) {
    if (isset($idpentry['name'])) {
        $this->includeInlineTranslation('idpname_'.$idpentry['entityid'], $idpentry['name']);
    } elseif (isset($idpentry['OrganizationDisplayName'])) {
        $this->includeInlineTranslation(
            'idpname_'.$idpentry['entityid'],
            $idpentry['OrganizationDisplayName']
        );
    }
    if (isset($idpentry['description'])) {
        $this->includeInlineTranslation('idpdesc_'.$idpentry['entityid'], $idpentry['description']);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title lang="en">SPID Smart Button</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/{{SERVICENAME}}/css/agid-spid-enter.css">
        <link rel="icon" type="image/ico" href="/{{SERVICENAME}}/img/favicon.ico">
    </head>
    <body>

        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div id="infomodal" class="modal"></div>
        <div id="agid-spid-enter"></div>
        <script type="text/javascript">
        <?php
            echo 'var config = {';
            foreach ($this->data['idplist'] as $idpentry) {

                $name = htmlspecialchars($this->t('idpname_'.$idpentry['entityid']));
                $url =  $this->data['urlpattern'] . 
                        '?entityID=' . urlencode(htmlspecialchars($this->data['entityID'])) . 
                        '&return=' . urlencode(htmlspecialchars($this->data['return'])) . 
                        '&returnIDParam=' . urlencode(htmlspecialchars($this->data['returnIDParam'])) . 
                        '&idp_' . $idpentry['entityid'] . '=' . 'idp_' . $idpentry['entityid'];
                $title = htmlspecialchars($this->t('idpdesc_'.$idpentry['entityid']));
                //$iconUrl = \SimpleSAML\Utils\HTTP::resolveURL($idpentry['icon']);
                $iconUrl = $idpentry['icon'];
                $logo = htmlspecialchars($iconUrl);

                echo '"'.$name.'": {"url": "'.$url.'","title": "'.$title.'","logo": "'.$logo.'"},';   
            }
            echo '};'
        ?>
        </script>
        <script type="text/javascript" src="/{{SERVICENAME}}/js/agid-spid-enter.js" ></script>
        <script type="text/javascript">
            agid_spid_enter();
            showPanel("agid-spid-panel-select");
        </script>

    </body>
</html>