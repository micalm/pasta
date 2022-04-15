<?php

namespace App\Http\Controllers\Web;

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
        $pasta = Pasta::findOrFail($uuid);

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

        return view('pasta.show', compact('pasta'));
    }
}
