<?php

declare(strict_types=1);

namespace Darflen\Framework\Support;

use RuntimeException;

class Crypt
{
    protected static string $key;
    protected static string $cipher;
    protected static string $mac;
    protected static int $mac_length = 32;

    public function __construct()
    {
        self::$key = config('security.encryption.key');
        self::$cipher = config('security.encryption.cipher');
        self::$mac = config('security.encryption.mac');
    }

    public static function encrypt(string $content)
    {
        $length = openssl_cipher_iv_length(self::$cipher);
        $iv = openssl_random_pseudo_bytes($length);
        $content = openssl_encrypt($content, self::$cipher, self::$key, 0, $iv);
        $mac = hash_hmac(self::$mac, $content, self::$key, true);
        return base64_encode($iv . $mac . $content);
    }

    public static function decrypt(string $content)
    {
        $content = base64_decode($content);
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $iv = substr($content, 0, $ivLength);
        $mac = substr($content, $ivLength, self::$mac_length);
        $content = substr($content, $ivLength + self::$mac_length);
        $calculated_mac = hash_hmac(self::$mac, $content, self::$key, true);
        if (hash_equals($mac, $calculated_mac)) {
            return openssl_decrypt($content, self::$cipher, self::$key, 0, $iv);
        }
        throw new RuntimeException('Invalid MAC');
    }
}
