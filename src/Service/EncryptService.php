<?php

namespace ISerranoDev\EncryptBundle\Service;

use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidDigestLength;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Alerts\InvalidMessage;
use ParagonIE\Halite\Alerts\InvalidSignature;
use ParagonIE\Halite\Alerts\InvalidType;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;

class EncryptService
{

    private string $hashKey;
    private string $method;
    private string $iv;
    private string $keyPath;

    public function __construct(
        string $hashKey,
        string $method,
        string $iv,
        string $keyPath
    ) {
        $this->hashKey = $hashKey;
        $this->method = $method;
        $this->iv = $iv;
        $this->keyPath = $keyPath;
    }

    /**
     * @param string $data
     * @return string
     * @throws CannotPerformOperation
     * @throws InvalidDigestLength
     * @throws InvalidKey
     * @throws InvalidMessage
     * @throws InvalidType
     * @throws \SodiumException
     */
    public function encryptData(string $data): string
    {

        $key = KeyFactory::loadEncryptionKey($this->keyPath);

        return Crypto::encrypt(
            new HiddenString($data),
            $key
        );
    }

    /**
     * @param string $encryptedData
     * @return string
     * @throws CannotPerformOperation
     * @throws InvalidDigestLength
     * @throws InvalidKey
     * @throws InvalidMessage
     * @throws InvalidSignature
     * @throws InvalidType
     * @throws \SodiumException
     */
    public function decryptData(string $encryptedData): string
    {

        $key = KeyFactory::loadEncryptionKey($this->keyPath);

        return Crypto::decrypt(
            $encryptedData,
            $key
        )->getString();

    }

    /**
     * @param string $data
     * @return string
     */
    public function hashData(string $data): string
    {

        $ciphertext = openssl_encrypt(
            strtoupper($data),
            $this->method,
            $this->hashKey,
            OPENSSL_RAW_DATA,
            $this->iv
        );

        return base64_encode($this->iv.$ciphertext);
    }

    public function isHashed(string $hashedData): bool {

        return str_starts_with($hashedData, substr(base64_encode($this->iv), 0, -3));
    }

    /**
     * @param string $encodedData
     * @return string
     */
    public function unHashData(string $encodedData): string
    {

        if (empty($encodedData)) {
            return $encodedData;
        }

        $data = base64_decode($encodedData);

        $ivsize = openssl_cipher_iv_length($this->method);
        $ciphertext = mb_substr($data, $ivsize, null, '8bit');

        return openssl_decrypt(
            $ciphertext,
            $this->method,
            $this->hashKey,
            OPENSSL_RAW_DATA,
            $this->iv
        );
    }


}
