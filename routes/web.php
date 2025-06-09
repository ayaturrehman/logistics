<?php

use App\Mail\QuoteCreated;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\StripePaymentController;

Route::get('/payment', function () {
    return view('welcome');
});


Route::get('/test-email', function () {
    $quote = Quote::first(); // Get a sample quote
    $paymentLink = "https://demo-payment.example.com/pay/" . $quote->id;
    // Mail::to('ayatuk@yahoo.com')->send(new QuoteCreated($quote, $paymentLink));
    Mail::to('ayatuk@yahoo.com')->queue(new QuoteCreated($quote, $paymentLink));
    dump('Email sent!');
});


Route::get('/test-queue', function () {
    $quote = Quote::latest()->first(); // Get a sample quote
    $stripeController = new StripePaymentController();
    $checkoutResponse = $stripeController->createCheckoutSession(new Request([
        'quote_id' => $quote->id
    ]));
    // echo 'api_Key'. config('services.stripe.key');
    return $responseData = $checkoutResponse;
});


Route::get('/stripe-payment', function () {
    $stripeController = new StripePaymentController();
    $result = $stripeController->capturePayment(9);
    return $result;
});
