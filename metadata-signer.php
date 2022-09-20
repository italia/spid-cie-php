<?php

require_once('spid-php.php');
require_once('vendor/simplesamlphp/simplesamlphp/config/authsources.php');

use SimpleSAML\Metadata\Signer;

//php metadata-signer.php metadata.xml metadata-signed.xml service
$metadata_in        = (count($argv) > 0 && $argv[1])? $argv[1] : 'metadata.xml';
$metadata_out       = (count($argv) > 0 && $argv[2])? $argv[2] : 'metadata-signed.xml';
$metadata_service   = (count($argv) > 0 && $argv[3])? $argv[3] : 'service';

$config[$metadata_service]['privatekey'] = 'spid-sp.pem';
$config[$metadata_service]['certificate'] = 'spid-sp.crt';

$signer = new Signer();
$xml = file_get_contents($metadata_in);
$xml = Signer::sign($xml, $config[$metadata_service], 'SAML 2 SP');
file_put_contents($metadata_out, $xml);
