<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Box;

class BoxesController extends BaseController
{
    public function index()
    {
        return view('index', [
            'boxes' => Box::get()
        ]);
    }

    public function show(Box $box)
    {
        return view('box', [
            'box' => $box->with('items')->first()
        ]);
    }
}