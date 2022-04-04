<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class PastaCreateController extends Controller
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

    public function handle() {
        return view('pasta.create');
    }
}
