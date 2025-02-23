<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\VehicleTypeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF Cookie Set']);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('businesses', BusinessController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('vehicle-types', VehicleTypeController::class);
    Route::post('vehicle-rate', [VehicleTypeController::class, 'getQuotes']);
    Route::apiResource('fares', FareController::class);
    Route::apiResource('quotes', QuoteController::class);
});

Route::post('vehiclerate', [VehicleTypeController::class, 'getQuotes']);
Route::post('quotes', [QuoteController::class, 'storewebsite']);

