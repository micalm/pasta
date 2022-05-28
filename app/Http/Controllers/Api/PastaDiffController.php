<?php

namespace App\Http\Controllers\Api;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use App\Models\Pasta;

class PastaDiffController extends Controller
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

    public function handle(Request $request, $uuid)
    {
        $key = $request->key;
        $parentKey = $request->parentKey;
        $pasta = Pasta::find($uuid);

        if (empty($pasta) || empty($pasta->parent)) {
            throw new NotFoundHttpException('Pasta not found.');
        }

        if ($key === null && $pasta->encrypted || $parentKey === null && $pasta->parent->encrypted) {
            throw new NotFoundHttpException('Pasta not found.');
        }

        if (!empty($key)) {
            try {
                $pasta = Pasta::decrypt($pasta, $key);
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Pasta not found.');
            }
        }
        if (!empty($parentKey)) {
            try {
                $parent = Pasta::decrypt($pasta->parent, $parentKey);
            } catch (DecryptException $e) {
                throw new NotFoundHttpException('Pasta not found.');
            }
        }

        return [
            'pasta' => $pasta,
            'parent' => $pasta->parent,
            'diff' => $pasta->getParentDiff(),
        ];
    }
}
