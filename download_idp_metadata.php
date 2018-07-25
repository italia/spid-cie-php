#!/usr/bin/php
<?php
// downloads the metadata for all current idps from the registry
// and stores them all in the idp_metadata directory
//
// prerequisites:
//   mkdir -p idp_metadata
//   sudo apt install php-curl
//
// Copyright (c) 2018, Paolo Greppi <paolo.greppi@simevo.com>
// License: BSD 3-Clause

$idp_list_url = 'https://registry.spid.gov.it/assets/data/idp.json';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $idp_list_url);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
echo "Contacting $idp_list_url" . PHP_EOL;
$json = curl_exec($ch);
curl_close($ch);
$idps = json_decode($json);

foreach ($idps->data as $idp) {
    $metadata_url = $idp->metadata_url;
    $ipa_entity_code = $idp->ipa_entity_code;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $metadata_url);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    echo "Contacting $metadata_url" . PHP_EOL;
    $xml = curl_exec($ch);
    curl_close($ch);
    $file = "idp_metadata/$ipa_entity_code.xml";
    file_put_contents($file, $xml);
}
