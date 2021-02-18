<?php

$config = array(
    'admin' => array(
        'core:AdminPassword',
    ),

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

        /* Configurazione attributi per metadata */
        'attributes.index' => {{ACSINDEX}},
        //'attributes.isDefault' => true,
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',
        'attributes' => array({{ATTRIBUTES}}),

        /* AttributeConsumingServiceIndex richiesto in AuthnRequest */
        'AttributeConsumingServiceIndex' => {{ACSINDEX}},
        'acs.Bindings' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
        'NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',

        /* ContactPerson according to SPID Avviso n.29 v3 */
        'contacts' => array(
            array(
                'contactType'       => 'other',
                'spid'              => true,
                'spid.codeType'     => {{ORGANIZATIONCODETYPE}},
                'spid.codeValue'    => {{ORGANIZATIONCODE}},
                'company'           => {{ORGANIZATIONNAME}},
                'emailAddress'      => {{ORGANIZATIONEMAILADDRESS}},
                'telephoneNumber'   => {{ORGANIZATIONTELEPHONENUMBER}}
            ),
            array(
                'contactType'       => 'billing',
                'fpa'               => true,
                'fpa.IdPaese'       => {{FPAIDPAESE}}, 
                'fpa.IdCodice'      => {{FPAIDCODICE}},
                'fpa.Denominazione' => {{FPADENOMINAZIONE}},
                'fpa.Indirizzo'     => {{FPAINDIRIZZO}},
                'fpa.NumeroCivico'  => {{FPANUMEROCIVICO}},
                'fpa.CAP'           => {{FPACAP}},
                'fpa.Comune'        => {{FPACOMUNE}},
                'fpa.Provincia'     => {{FPAPROVINCIA}},
                'fpa.Nazione'       => {{FPANAZIONE}},
                'company'           => {{FPAORGANIZATIONNAME}},
                'emailAddress'      => {{FPAORGANIZATIONEMAILADDRESS}},
                'telephoneNumber'   => {{FPAORGANIZATIONTELEPHONENUMBER}}
            )
        )
    )
);
