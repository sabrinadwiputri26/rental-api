<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/rentals', [RentalController::class, 'index']);
Route::post('/rentals/tambah-data', [RentalController::class, 'store']);
Route::get('generate-token', [RentalController::class, 'createToken']);
Route::get('/rentals/show/trash', [RentalController::class, 'trash']);
Route::get('/rentals/{id}', [RentalController::class, 'show']);
Route::patch('/rentals/update/{id}', [RentalController::class, 'update']);
Route::delete('/rentals/delete/{id}', [RentalController::class, 'destroy']);
Route::get('rentals/trash/restore/{id}', [RentalController::class, 'restore']);
Route::get('/rentals/trash/delete/permanent/{id}', [RentalController::class, 'permanentDelete']);