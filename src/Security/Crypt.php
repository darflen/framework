<?php

declare(strict_types=1);

namespace Darflen\Framework\Security;

use RuntimeException;

class Crypt
{
    protected string $key;
    protected string $cipher;
    protected string $mac;

    public function __construct()
    {
        $this->key = config('security.encryption.key');
        $this->cipher = config('security.encryption.cipher');
        $this->mac = config('security.encryption.mac');
    }

    public function encryptValue(string $content): string
    {
        $length = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($length);
        $content = openssl_encrypt($content, $this->cipher, $this->key, 0, $iv);
        $mac = hash_hmac($this->mac, $content, $this->key, true);
        return base64_encode($iv . $mac . $content);
    }

    public function decryptValue(string $content): string
    {
        $mac_length = 32;
        $content = base64_decode($content);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = substr($content, 0, $ivLength);
        $mac = substr($content, $ivLength, $mac_length);
        $content = substr($content, $ivLength + $mac_length);
        $calculated_mac = hash_hmac($this->mac, $content, $this->key, true);
        if (hash_equals($mac, $calculated_mac)) {
            return openssl_decrypt($content, $this->cipher, $this->key, 0, $iv);
        }
        throw new RuntimeException('Invalid Encrypted Value');
    }
}
