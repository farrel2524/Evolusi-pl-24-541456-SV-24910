<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function boomesport() {
        return view('boom');
    }

    public function prxesport() {
        return view(view: 'prx');
    }

}
