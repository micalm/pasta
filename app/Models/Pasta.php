<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidKeys;
use App\Encryption\PasswordEncrypter;

class Pasta extends Model
{
    use UsesUuidKeys;

    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'author',
        'content',
        'language',
    ];

    protected $hidden = [
        'salt',
    ];

    protected $casts = [
        'encrypted' => 'boolean',
    ];

    public static function decrypt(Pasta &$pasta, string $password): Pasta
    {
        $encrypter = new PasswordEncrypter($password, base64_decode($pasta->salt));
        $pasta->content = $encrypter->decrypt($pasta->content);

        return $pasta;
    }

    public static function encrypt(Pasta &$pasta, string $password): Pasta
    {
        $salt = static::getSalt();
        $encrypter = new PasswordEncrypter($password, $salt);
        $pasta->content = $encrypter->encrypt($pasta->content);
        $pasta->salt = base64_encode($salt);
        $pasta->encrypted = true;

        return $pasta;
    }

    private static function getSalt(int $length = null): string
    {
        return \random_bytes($length ?? SODIUM_CRYPTO_PWHASH_SALTBYTES);
    }
}
