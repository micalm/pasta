<?php

namespace App\Models;

use Carbon\Carbon;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder as Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\UsesUuidKeys;
use App\Encryption\PasswordEncrypter;

class Pasta extends Model
{
    use UsesUuidKeys;

    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'parent_id',
        'author',
        'content',
        'language',
        'burn_on_read',
        'expires_at',
    ];

    protected $hidden = [
        'salt',
    ];

    protected $casts = [
        'encrypted' => 'boolean',
        'burn_on_read' => 'boolean',
    ];

    protected static function booted()
    {
        static::retrieved(function (Pasta $pasta) {
            // Immediately delete when fetched after expiry
            if (!empty($pasta->expires_at)) {
                if (now()->greaterThanOrEqualTo(new Carbon($pasta->expires_at))) {
                    $pasta->delete();
                    throw new NotFoundHttpException('Pasta not found.');
                }
            }

            return null;
        });
    }

    public function parent()
    {
        return $this->hasOne(Pasta::class, 'uuid', 'parent_id');
    }

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

    public function getDiff(Pasta $pasta, Pasta $parent, $key = null, $parentKey = null)
    {
        $builder = new Builder([
            'collapseRanges'      => true,
            'commonLineThreshold' => 6,
            'contextLines'        => 3,
            'fromFile'            => $parent->uuid,
            'fromFileDate'        => $parent->created_at->toString(),
            'toFile'              => $pasta->uuid,
            'toFileDate'          => $pasta->created_at->toString(),
        ]);
        $differ = new Differ($builder);

        return $differ->diff($parent->content, $pasta->content);
    }

    public function getParentDiff($key = null, $parentKey = null)
    {
        return $this->getDiff($this, $this->parent, $key, $parentKey);
    }
}
