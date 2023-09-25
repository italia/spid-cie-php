    'cie' => array(
        'saml:SP',
        'privatekey' => 'cie-sp.pem',
        'certificate' => 'cie-sp.crt',

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

        
        'acs.Bindings' => [
            'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ], 
        'AuthnContextComparison' => 'exact',
        
        'ForceAuthn' => true,

        'sign.authnrequest' => true,
        'sign.logout' => true,
        'AuthnRequestsSigned' => true,
        'WantAssertionsSigned' => true,

        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',
        'attributes' => array (
            0 => 'fiscalNumber',
            1 => 'name',
            2 => 'familyName',
            3 => 'dateOfBirth',
        ),
        
        'metadata.sign.enable' => true,
        'metadata.sign.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.supported.protocols' => [
            'urn:oasis:names:tc:SAML:2.0:protocol'
        ],
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

        'NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',

        'contacts' => array (
            0 => 
            array (
            'contactType' => 'administrative',
            'company' => {{ORGANIZATIONNAME}},
            'emailAddress' => {{ORGANIZATIONEMAILADDRESS}},
            'telephoneNumber' => {{ORGANIZATIONTELEPHONENUMBER}},
            'extensions' => 
                array (
                    'ns' => 'cie:https://www.cartaidentita.interno.gov.it/saml-extensions',
                    'elements' => array(
                        'cie:Public' => NULL,
                        'cie:IPACode' => {{ORGANIZATIONCODE}},
                        'cie:Municipality' => {{ORGANIZATIONMUNICIPALITY}},
                        'cie:Province' => {{ORGANIZATIONPROVINCE}},
                        'cie:Country' => {{ORGANIZATIONCOUNTRY}},
                    )
                ),
            ),
        )
    ),
