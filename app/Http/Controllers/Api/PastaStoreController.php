<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Pasta;

class PastaStoreController extends Controller
{
    protected $rules = [
        'author' => 'min:1|max:255',
        'content' => 'required|min:1|max:16777215',
        'key' => 'nullable|min:8',
    ];

    /**
     * Create a new controller instance.
    *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->rules,
        );

        if ($validator->fails()) {
            abort(422, join(' ', $validator->errors()->all()));
        }

        $pasta = new Pasta([
            'author' => $request->author,
            'content' => $request->content,
            'language' => $request->language,
            'encrypted' => $request->has('key'),
        ]);

        if ($request->has('key')) {
            try {
                $pasta = Pasta::encrypt($pasta, $request->key);
            } catch (\Exception $e) {
                abort(500);
            }
        }

        $pasta->save();

        return $pasta;
    }
}
