<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\GoodsTypeController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TransportTypeController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\DashboardController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF Cookie Set']);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('businesses', BusinessController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('vehicle-types', VehicleTypeController::class);
    Route::post('vehicle-rate', [VehicleTypeController::class, 'getQuotes']);
    Route::apiResource('fares', FareController::class);
    Route::apiResource('quotes', QuoteController::class);
});

Route::post('vehiclerate', [VehicleTypeController::class, 'getQuotes']);
Route::post('quotesstore', [QuoteController::class, 'storewebsite']);

Route::get('goods-types', [GoodsTypeController::class, 'index']);
Route::get('transport-types', [TransportTypeController::class, 'index']);
Route::post('/verify-payment', [QuoteController::class, 'updatePaymentStatusBySession']);
Route::get('/payments/create-checkout-session', [StripePaymentController::class, 'createCheckoutSession']);
Route::post('/quotes/{id}/capture-payment', [QuoteController::class, 'capturePayment'])->name('quotes.capture-payment');




Route::get('/payment/success', function (Request $request) {
    // return view('payment.success');
})->name('payment.success');
Route::get('/payment/cancel', function (Request $request) {
    // return view('payment.cancel');
})->name('payment.cancel');


Route::get('/payments', function (Request $request) {
    return $payload = @file_get_contents('php://input');
})->name('payment.failed');