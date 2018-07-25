<?php

$config = array(

    // An authentication source 
    'admin' => array(
        'core:AdminPassword',
    ),

    // An authentication source
    'service-l1' => array(
        'saml:SP',
        'privatekey' => 'spid-sp.pem',
        'certificate' => 'spid-sp.crt',
        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => {{ENTITYID}},
        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => null,
        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => null,

        /* Impostare il livello di spid che si vuole (1,2,3)  per il servizio */
        'AuthnContextClassRef' =>
        array(
            'https://www.spid.gov.it/SpidL1',
        ),

        'AuthnContextComparison' => 'exact',

        /*Per autenticazione superiori a SPID Livello 1 occorre specificare 'ForceAuthn' => true */
        'ForceAuthn' => false,

        'simplesaml.attributes' => false,

        'AttributeConsumingServiceIndex' => {{ACSINDEX}},
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.sign.enable' => true,
        'metadata.sign.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.supported.protocols' => array('urn:oasis:names:tc:SAML:2.0:protocol'),

        'sign.authnrequest' => true,
        'sign.logout' => true,
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',

        /* Per avere gli attributi richiesti tramite il metadata (codice fiscale, ecc) - specificare solo gli attributi necessari */
        'attributes' => array(
            'spidCode',
            'name',
            'familyName',
            'placeOfBirth',
            'countyOfBirth',
            'dateOfBirth',
            'gender',
            'fiscalNumber',
            'mobilePhone',
            'email',
            'address',
            'digitalAddress'
        ),
        // Attributi obbligatori richiesti in fase di autenticazione
        'attributes.required' => array(
        ),
        'ProtocolBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        'acs.Bindings' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST', 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post'),
        'SingleLogoutServiceBinding' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
        'NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'
    ),



    // An authentication source 
    'service-l2' => array(
        'saml:SP',
        'privatekey' => 'spid-sp.pem',
        'certificate' => 'spid-sp.crt',
        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => {{ENTITYID}},
        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => null,
        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => null,

        /* Impostare il livello di spid che si vuole (1,2,3)  per il servizio */
        'AuthnContextClassRef' =>
        array(
            'https://www.spid.gov.it/SpidL2',
        ),

        'AuthnContextComparison' => 'exact',

        /*Per autenticazione superiori a SPID Livello 1 occorre specificare 'ForceAuthn' => true */
        'ForceAuthn' => false,

        'simplesaml.attributes' => false,

        'AttributeConsumingServiceIndex' => {{ACSINDEX}},
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.sign.enable' => true,
        'metadata.sign.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.supported.protocols' => array('urn:oasis:names:tc:SAML:2.0:protocol'),

        'sign.authnrequest' => true,
        'sign.logout' => true,
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',

        /* Per avere gli attributi richiesti tramite il metadata (codice fiscale, ecc) - specificare solo gli attributi necessari */
        'attributes' => array(
            'spidCode',
            'name',
            'familyName',
            'placeOfBirth',
            'countyOfBirth',
            'dateOfBirth',
            'gender',
            'fiscalNumber',
            'mobilePhone',
            'email',
            'address',
            'digitalAddress'
        ),
        // Attributi obbligatori richiesti in fase di autenticazione
        'attributes.required' => array(
        ),
        'acs.Bindings' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
        'SingleLogoutServiceBinding' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
		'NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
    ),    


    // An authentication source 
    'service-l3' => array(
        'saml:SP',
        'privatekey' => 'spid-sp.pem',
        'certificate' => 'spid-sp.crt',
        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => {{ENTITYID}},
        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => null,
        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => null,

        /* Impostare il livello di spid che si vuole (1,2,3)  per il servizio */
        'AuthnContextClassRef' =>
        array(
            'https://www.spid.gov.it/SpidL3',
        ),

        'AuthnContextComparison' => 'exact',

        /*Per autenticazione superiori a SPID Livello 1 occorre specificare 'ForceAuthn' => true */
        'ForceAuthn' => false,

        'simplesaml.attributes' => false,

        'AttributeConsumingServiceIndex' => {{ACSINDEX}},
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.sign.enable' => true,
        'metadata.sign.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.supported.protocols' => array('urn:oasis:names:tc:SAML:2.0:protocol'),

        'sign.authnrequest' => true,
        'sign.logout' => true,
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',

        /* Per avere gli attributi richiesti tramite il metadata (codice fiscale, ecc) - specificare solo gli attributi necessari */
        'attributes' => array(
            'spidCode',
            'name',
            'familyName',
            'placeOfBirth',
            'countyOfBirth',
            'dateOfBirth',
            'gender',
            'fiscalNumber',
            'mobilePhone',
            'email',
            'address',
            'digitalAddress'
        ),
        // Attributi obbligatori richiesti in fase di autenticazione
        'attributes.required' => array(
        ),
        'acs.Bindings' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
        'SingleLogoutServiceBinding' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
		'NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
    )     
);
