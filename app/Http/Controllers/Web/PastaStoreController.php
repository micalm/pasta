<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
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
        $this->validate($request, $this->rules);

        $pasta = new Pasta([
            'parent_id' => $request->parent_id,
            'author' => $request->author,
            'content' => $request->content,
            'language' => $request->language,
            'encrypted' => $request->has('key'),
            // @todo handle first show of burn-on-read pastas for the creator
            'burn_on_read' => false //$request->burn_on_read ?? false,
        ]);

        if ($request->has('key') && $request->key !== null) {
            try {
                $pasta = Pasta::encrypt($pasta, $request->key);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        $pasta->save();

        return $pasta;
    }
}
