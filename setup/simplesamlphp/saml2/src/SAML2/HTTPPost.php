<?php

declare(strict_types=1);

namespace SAML2;

use SimpleSAML\Configuration;

/**
 * Class which implements the HTTP-POST binding.
 *
 * @package SimpleSAMLphp
 */
class HTTPPost extends Binding
{
    /**
     * Send a SAML 2 message using the HTTP-POST binding.
     *
     * Note: This function never returns.
     *
     * @param \SAML2\Message $message The message we should send.
     * @return void
     */
    public function send(Message $message) : void
    {
        if ($this->destination === null) {
            $destination = $message->getDestination();
            if ($destination === null) {
                throw new \Exception('Cannot send message, no destination set.');
            }
        } else {
            $destination = $this->destination;
        }

        $globalConfig = Configuration::getInstance();
        $secretSalt = $globalConfig->getString('secretsalt');

        $encryptedRelayState = $message->getRelayState();

        $ivSize = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($ivSize);
        $relayState = base64_encode($iv . openssl_encrypt($encryptedRelayState, "aes-256-cbc", $secretSalt, 0, $iv));

        $msgStr = $message->toSignedXML();

        Utils::getContainer()->debugMessage($msgStr, 'out');
        $msgStr = $msgStr->ownerDocument->saveXML($msgStr);

        $msgStr = base64_encode($msgStr);

        if ($message instanceof Request) {
            $msgType = 'SAMLRequest';
        } else {
            $msgType = 'SAMLResponse';
        }

        $post = [];
        $post[$msgType] = $msgStr;

        if ($relayState !== null) {
            $post['RelayState'] = $relayState;
        }

        Utils::getContainer()->postRedirect($destination, $post);
    }


    /**
     * Receive a SAML 2 message sent using the HTTP-POST binding.
     *
     * Throws an exception if it is unable receive the message.
     *
     * @return \SAML2\Message The received message.
     * @throws \Exception
     */
    public function receive(): Message
    {
        if (array_key_exists('SAMLRequest', $_POST)) {
            $msgStr = $_POST['SAMLRequest'];
        } elseif (array_key_exists('SAMLResponse', $_POST)) {
            $msgStr = $_POST['SAMLResponse'];
        } else {
            throw new \Exception('Missing SAMLRequest or SAMLResponse parameter.');
        }

        $msgStr = base64_decode($msgStr);

        $xml = new \DOMDocument();
        $xml->loadXML($msgStr);
        $msgStr = $xml->saveXML();

        $document = DOMDocumentFactory::fromString($msgStr);
        Utils::getContainer()->debugMessage($document->documentElement, 'in');
        if (!$document->firstChild instanceof \DOMElement) {
            throw new \Exception('Malformed SAML message received.');
        }

        $msg = Message::fromXML($document->firstChild);

        if (array_key_exists('RelayState', $_POST)) {

            $globalConfig = Configuration::getInstance();
            $secretSalt = $globalConfig->getString('secretsalt');

            $encryptedRelayState = $_POST['RelayState'];

            $ivSize = openssl_cipher_iv_length("aes-256-cbc");
            $decodedData = base64_decode($encryptedRelayState);
            $iv = substr($decodedData, 0, $ivSize);

            $relayState = openssl_decrypt(substr($decodedData, $ivSize), "aes-256-cbc", $secretSalt, 0, $iv);

            $msg->setRelayState($relayState);
        }

        return $msg;
    }
}
