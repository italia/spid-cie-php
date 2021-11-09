<?php

require_once('spid-php.php');
require_once('vendor/simplesamlphp/simplesamlphp/config/authsources.php');

use SimpleSAML\Metadata\Signer;

$metadata_in = $argv[0] || 'metadata.xml';
$metadata_out = $argv[1] || 'metadata-signed.xml';
$metadata_service = $argv[2] || 'service';

//$config['service']['privatekey'] = 'fake.pem';
//$config['service']['certificate'] = 'fake.crt';

$signer = new Signer();
$xml = file_get_contents('metadata.xml');
$xml = Signer::sign($xml, $config['service'], 'SAML 2 SP');
file_put_contents('metadata-signed.xml', $xml);
