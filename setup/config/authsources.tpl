<?php 

use SimpleSAML\Configuration;
use SimpleSAML\Database;

define('AUTHSOURCES_DATABASE_TABLE', 'authsources');

$ssp_config = Configuration::getInstance();
$authsources_storage = $ssp_config->getString('authsources.storage', 'file');

if($authsources_storage=='database') {

    $db = Database::getInstance();
    $authsources_database_table = $ssp_config->getString('authsources.database_table', AUTHSOURCES_DATABASE_TABLE);

    // create table if not exists
    $db->write(sprintf("
        CREATE TABLE IF NOT EXISTS $authsources_database_table (
            `id` VARCHAR(255) PRIMARY KEY NOT NULL, 
            `entity_data` JSON NOT NULL,
            `_disabled` enum('N','Y') NOT NULL DEFAULT 'N'
        );
    "));
    
    // get config from database
    $statement = $db->read("SELECT `id`, `entity_data` FROM `" . $authsources_database_table . "` WHERE `_disabled`='N'");
    $authsources = $statement->fetchAll();
    
    $config = [
        // This is a authentication source which handles admin authentication.
        'admin' => [
            // The default is to use core:AdminPassword, but it can be replaced with
            // any authentication source.

            'core:AdminPassword',
        ],
    ];

    // compile config
    foreach($authsources as $as) {
        $config[$as['id']] = json_decode($as['entity_data'], true);
    }
    
} else {

    $config = array(
        'admin' => array(
            'core:AdminPassword',
        ),

        {{AUTHSOURCE_SPID}}
        
        {{AUTHSOURCE_CIE}}
    );

}
