<?php

use App\Models\Plantd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// JSON endpoint for Vue and Postman: GET /api/plants
Route::get('/plants', fn () => response()->json(['data' => Plantd::orderBy('id')->get()]));
