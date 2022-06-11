<?php

namespace App\Http\Controllers\Api;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use App\Models\Pasta;

class PastaShowController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle($uuid, $key = null)
    {
        $pasta = Pasta::find($uuid);

        if (empty($pasta)) {
            throw new NotFoundHttpException('Pasta not found.');
        }

        if ($key === null && $pasta->encrypted) {
            throw new NotFoundHttpException('Pasta not found.');
        }

        if (!empty($key)) {
            try {
                $pasta = Pasta::decrypt($pasta, $key);
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Pasta not found.');
            }
        }

        if ($pasta->burn_on_read) {
            $pasta->delete();
        }
        return $pasta;
    }
}
