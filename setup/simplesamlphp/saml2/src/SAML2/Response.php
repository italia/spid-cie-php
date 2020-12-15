<?php

namespace SAML2;

/**
 * Class for SAML 2 Response messages.
 *
 * @package SimpleSAMLphp
 */
class Response extends StatusResponse
{
    /**
     * The assertions in this response.
     */
    private $assertions;

    /**
     * Constructor for SAML 2 response messages.
     *
     * @param \DOMElement|null $xml The input message.
     */
    public function __construct(\DOMElement $xml = null)
    {
        parent::__construct('Response', $xml);

        $this->assertions = array();

        if ($xml === null) {
            return;
        }

        for ($node = $xml->firstChild; $node !== null; $node = $node->nextSibling) {
            if ($node->namespaceURI !== Constants::NS_SAML) {
                continue;
            }

            if ($node->localName === 'Assertion') {
                $this->assertions[] = new Assertion($node);
            } elseif ($node->localName === 'EncryptedAssertion') {
                $this->assertions[] = new EncryptedAssertion($node);
            }
        }

        $statusCodeNode = Utils::xpQuery($xml, './saml_protocol:Status/saml_protocol:StatusCode');
        $statusCode = $statusCodeNode[0]->getAttribute("Value");
        $statusMessageNode = Utils::xpQuery($xml, './saml_protocol:Status/saml_protocol:StatusMessage');
        $statusMessage = $statusMessageNode[0]->nodeValue;

        if($statusCode=="urn:oasis:names:tc:SAML:2.0:status:Success") {

            /* SPID CUSTOM : Assertion Issuer cannot be blank */
            $assertionIssuer = Utils::xpQuery($xml, './saml_assertion:Assertion/saml_assertion:Issuer');
            $this->assertionIssuer = new XML\saml\Issuer($assertionIssuer[0]);
            if($assertionIssuer[0]!=null) $this->assertionIssuer->Format = $assertionIssuer[0]->getAttribute('Format');

            if($this->assertionIssuer->value == null || $this->assertionIssuer->value == "") {
                throw new \Exception('Missing Issuer on Assertion');
            }

            /* SPID CUSTOM : Attribute Format of Assertion Issuer */
            if($this->assertionIssuer->Format == null || $this->assertionIssuer->Format != "urn:oasis:names:tc:SAML:2.0:nameid-format:entity") {
                throw new \Exception('Attribute Format of Issuer on Assertion was not valid.');
            }

            /* SPID CUSTOM : check conditions */
            $conditions = Utils::xpQuery($xml, './saml_assertion:Assertion/saml_assertion:Conditions');
            if($conditions==null || $conditions[0]->nodeValue==null || trim($conditions[0]->nodeValue)=="") {
                throw new \Exception('Missing Conditions on Assertion');
            }
            if($conditions[0]->getAttribute("NotBefore")==null || trim($conditions[0]->getAttribute("NotBefore"))=="") {
                throw new \Exception('Missing Attribute NotBefore of Conditions on Assertion');
            }
            if($conditions[0]->getAttribute("NotOnOrAfter")==null || trim($conditions[0]->getAttribute("NotOnOrAfter"))=="") {
                throw new \Exception('Missing Attribute NotOnOrAfter of Conditions on Assertion');
            }

            /* SPID CUSTOM : check attributes */
            $attributeStatement = Utils::xpQuery($xml, './saml_assertion:Assertion/saml_assertion:AttributeStatement');        
            if($attributeStatement==null || $attributeStatement[0]->nodeValue==null || trim($attributeStatement[0]->nodeValue)=="") {
                throw new \Exception('Missing AttributeStatement on Assertion');
            }

            /* SPID CUSTOM : check attributes */
            $authnContextClassRef = Utils::xpQuery($xml, './saml_assertion:Assertion/saml_assertion:AuthnStatement/saml_assertion:AuthnContext/saml_assertion:AuthnContextClassRef');
            if($authnContextClassRef==null || $authnContextClassRef[0]->nodeValue==null || trim($authnContextClassRef[0]->nodeValue)=="") {
                throw new \Exception('Missing AuthnContextClassRef on Assertion');
            }
            if($authnContextClassRef[0]->nodeValue!="https://www.spid.gov.it/SpidL1"
                && $authnContextClassRef[0]->nodeValue!="https://www.spid.gov.it/SpidL2"
                && $authnContextClassRef[0]->nodeValue!="https://www.spid.gov.it/SpidL3") {
                    throw new \Exception('AuthnContextClassRef was not valid.');
            }

            $inResponseTo = $xml->getAttribute('InResponseTo');
            $stateID = substr($inResponseTo, 8);

            $state = \SimpleSAML_Auth_State::loadState($stateID, 'saml:sp:sso', true);
            $req_authnContextClassRef = $state["saml:AuthnContextClassRef"];
            $req_authnContextComparison = $state["saml:AuthnContextComparison"];
            $res_authnContextClassRef = $authnContextClassRef[0]->nodeValue;

            $req_level = intval(substr($req_authnContextClassRef, -1));
            $res_level = intval(substr($res_authnContextClassRef, -1));

            switch($req_authnContextComparison) {
                case \SAML2\Constants::COMPARISON_EXACT:
                    if($req_authnContextClassRef!=$res_authnContextClassRef) {
                        throw new \Exception('AuthnContextClassRef can not be accepted. Requested EXACT ' . $req_authnContextClassRef);
                    }
                break;

                case \SAML2\Constants::COMPARISON_MINIMUM:
                    if($res_level < $req_level) {
                        throw new \Exception('AuthnContextClassRef can not be accepted. Requested MINIMUM ' . $req_authnContextClassRef);
                    }
                break;

                case \SAML2\Constants::COMPARISON_MAXIMUM:
                    if($res_level > $req_level) {
                        throw new \Exception('AuthnContextClassRef can not be accepted. Requested MAXIMUM ' . $req_authnContextClassRef);
                    }
                break;

            }
            
        } else {
            //throw new \Exception($statusMessage);
        }
    }

    /**
     * Retrieve the assertions in this response.
     *
     * @return \SAML2\Assertion[]|\SAML2\EncryptedAssertion[]
     */
    public function getAssertions()
    {
        return $this->assertions;
    }

    /**
     * Set the assertions that should be included in this response.
     *
     * @param \SAML2\Assertion[]|\SAML2\EncryptedAssertion[] The assertions.
     */
    public function setAssertions(array $assertions)
    {
        $this->assertions = $assertions;
    }

    /**
     * Convert the response message to an XML element.
     *
     * @return \DOMElement This response.
     */
    public function toUnsignedXML()
    {
        $root = parent::toUnsignedXML();

        /** @var \SAML2\Assertion|\SAML2\EncryptedAssertion $assertion */
        foreach ($this->assertions as $assertion) {
            $assertion->toXML($root);
        }

        return $root;
    }
}
