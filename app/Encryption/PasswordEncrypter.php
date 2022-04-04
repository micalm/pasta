<?php

namespace App\Encryption;

use Illuminate\Encryption\Encrypter;

class PasswordEncrypter
{
    private $encrypter;

    public function __construct(string $password, string $salt, $cipher = 'aes-256-gcm')
    {
        // Derive a key from the password
        $key = sodium_crypto_pwhash(
            32,
            $password,
            $salt,
            \SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            \SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,
            \SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );

        $this->encrypter = new Encrypter($key, $cipher);
    }

    public function encrypt($value)
    {
        return $this->encrypter->encryptString($value);
    }

    public function decrypt($value)
    {
        return $this->encrypter->decryptString($value);
    }
}
