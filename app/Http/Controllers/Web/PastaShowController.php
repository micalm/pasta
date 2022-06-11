<?php

namespace App\Http\Controllers\Web;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
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

    public function handle(Request $request, $uuid, $key = null)
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

        $burnableFirstView = $request->session()->pull('firstView');
        if ($pasta->burn_on_read && $burnableFirstView !== $pasta->uuid) {
            $pasta->delete();
        }

        return view('pasta.show', compact('pasta'));
    }
}
