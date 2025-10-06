<?php

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;  
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Jose\Component\Signature\Serializer\CompactSerializer as JWSSerializer;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Encryption\Algorithm\KeyEncryption\A256KW;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A256CBCHS512;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEBuilder; 
use Jose\Component\Encryption\Serializer\JWESerializerManager;
use Jose\Component\Encryption\Serializer\CompactSerializer as JWESerializer;
use Jose\Component\Encryption\JWEDecrypter;

const TOKEN_PRIVATE_KEY = "../cert/spid-sp.pem";
const TOKEN_PUBLIC_CERT = "../cert/spid-sp.crt";
const DEFAULT_SECRET = "";
const DEFAULT_TOKEN_EXPIRATION_TIME = 1200;


abstract class ResponseHandler {

    function __construct($issuer, $config) {
        $this->issuer = $issuer;
        $this->config = $config;
        
        $this->privateKey = TOKEN_PRIVATE_KEY;
        $this->publicCert = TOKEN_PUBLIC_CERT;
    }

    function set($key, $value) {
        $this->$key = $value;
    }

    function get($key) {
        return $this->$key;
    }

    abstract public function sendResponse($redirect_uri, $data, $state);

    protected function makeJWE($payload, $exp_time, $iss, $aud, $secret): string {
        
        $iat        = new DateTimeImmutable();
        $exp_time   = $exp_time?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $exp        = $iat->modify("+".$exp_time." seconds")->getTimestamp();

        $data = [
            'iss'  => $iss,                                     // Issuer - spDomain
            'aud'  => $aud,                                     // Audience - Redirect_uri
            'iat'  => $iat->getTimestamp(),                     // Issued at: time when the token was generated
            'nbf'  => $iat->getTimestamp(),                     // Not before
            'exp'  => $exp,                                     // Expire
            'data' => $payload,                                 // Authentication Data
        ];

        $keyEncryptionAlgorithmManager = new AlgorithmManager([ new A256KW() ]); 
        $contentEncryptionAlgorithmManager = new AlgorithmManager([ new A256CBCHS512() ]);
        $compressionMethodManager = new CompressionMethodManager([ new Deflate() ]);

        $jweBuilder = new JWEBuilder(
            $keyEncryptionAlgorithmManager,
            $contentEncryptionAlgorithmManager,
            $compressionMethodManager
        );

        $jwk = JWKFactory::createFromSecret($secret?:DEFAULT_SECRET);

        $jwe = $jweBuilder
            ->create()
            ->withPayload(json_encode($data))
            ->withSharedProtectedHeader([
                'alg' => 'A256KW',
                'enc' => 'A256CBC-HS512',
                'zip' => 'DEF'
            ])
            ->addRecipient($jwk) 
            ->build();

        $serializer = new JWESerializer();
        $token = $serializer->serialize($jwe, 0); 

        return $token;
    }

    protected function makeJWS($payload, $exp_time, $iss, $aud, $jwk_pem): string {
        
        $iat        = new DateTimeImmutable();
        $exp_time   = $exp_time?: DEFAULT_TOKEN_EXPIRATION_TIME;
        $exp        = $iat->modify("+".$exp_time." seconds")->getTimestamp();

        $data = [
            'iss'  => $iss,                                     // Issuer - spDomain
            'aud'  => $aud,                                     // Audience - Redirect_uri
            'iat'  => $iat->getTimestamp(),                     // Issued at: time when the token was generated
            'nbf'  => $iat->getTimestamp(),                     // Not before
            'exp'  => $exp,                                     // Expire
            'data' => $payload,                                 // Authentication Data
        ];

        if(is_array($payload) && array_key_exists('fiscalNumber', $payload)) {        // Subject - fiscalNumber, only if exists
            $data['sub'] = $payload['fiscalNumber'];
        }

        $algorithmManager = new AlgorithmManager([new RS256()]);
        $jwk = JWKFactory::createFromKeyFile($jwk_pem);
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $jws = $jwsBuilder
            ->create() 
            ->withPayload(json_encode($data)) 
            ->addSignature($jwk, ['alg' => 'RS256']) 
            ->build(); 
        
        $serializer = new JWSSerializer(); 
        $token = $serializer->serialize($jws, 0); 
    
        return $token;
    }
}
