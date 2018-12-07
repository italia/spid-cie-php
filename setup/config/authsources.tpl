<?php

$config = array(

    // An authentication source 
    'service' => array(
        'saml:SP',
        'privatekey' => 'spid-sp.pem',
        'certificate' => 'spid-sp.crt',

        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => {{ENTITYID}},

        'name' => array('it'=> {{NAME}}),
        'description' => array('it'=> {{DESCRIPTION}}),
        'OrganizationName' => array('it'=> {{ORGANIZATIONNAME}}),
        'OrganizationDisplayName' => array('it'=> {{ORGANIZATIONDISPLAYNAME}}),
        'OrganizationURL' => array('it'=> {{ORGANIZATIONURL}}),

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
            'https://www.spid.gov.it/SpidL2',
            'https://www.spid.gov.it/SpidL3'
        ),

        'AuthnContextComparison' => 'exact',

        /* Per autenticazione superiori a SPID Livello 1 occorre specificare 'ForceAuthn' => true */
        'ForceAuthn' => true,

        'sign.authnrequest' => true,
        'sign.logout' => true,
        'AuthnRequestsSigned' => true,
        'WantAssertionsSigned' => true,

        'metadata.sign.enable' => true,
        'metadata.sign.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.supported.protocols' => array('urn:oasis:names:tc:SAML:2.0:protocol'),
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

        'attributes.index' => {{ACSINDEX}},
        'attributes.isDefault' => true,
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',

        /* Per avere gli attributi richiesti tramite il metadata (codice fiscale, ecc) - specificare solo gli attributi necessari */
        'attributes' => array({{ATTRIBUTES}}),

        'acs.Bindings' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
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
