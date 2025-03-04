<?php

use App\Mail\QuoteCreated;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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
