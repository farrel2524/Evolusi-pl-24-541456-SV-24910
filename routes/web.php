<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/plants', [PlantController::class, 'index'])->name('plants.index');
Route::get('/plants/create', [PlantController::class, 'create'])->name('plants.create');
Route::post('/plants', [PlantController::class, 'store'])->name('plants.store');
Route::get('/plants/{id}/edit', [PlantController::class, 'edit'])->name('plants.edit');
Route::put('/plants/{id}', [PlantController::class, 'update'])->name('plants.update');
Route::delete('/plants/{id}', [PlantController::class, 'destroy'])->name('plants.destroy');
