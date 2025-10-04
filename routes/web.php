<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/test-service-times', [App\Http\Controllers\ServiceTimeController::class, 'store']);