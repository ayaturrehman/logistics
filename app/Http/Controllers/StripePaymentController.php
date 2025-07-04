<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session;
use Stripe\PaymentLink;
use Stripe\Webhook;
use App\Mail\PaymentConfirmation;
use App\Mail\PaymentFailed;
use App\Mail\PaymentExpired;
use Stripe\Event as StripeEvent;


class StripePaymentController extends Controller
{
    // public function createCheckoutSession(Request $request)
    // {
    //     try {
    //         // Validate request
    //         $validated = $request->validate([
    //             'quote_id' => 'required|exists:quotes,id',
    //         ]);

    //         // Get quote details // where status is payment_status is not paid
    //         $quote = Quote::where('payment_status', '!=', 'paid')->findOrFail($validated['quote_id']);

    //         if ($quote->payment_status == 'paid') {
    //             return response()->json(['error' => 'Payment already made'], 400);
    //         }

    //         \Stripe\Stripe::setApiKey(config('services.stripe.key'));
    //         // \Stripe\Stripe::setApiKey('sk_test_51R2ZOqPt6oHLLigNFVQUYoKAPItaZPXdbYHVqSru5MqOHTWO9Q97WW4C7TFd8VTYvaPLiFmMnLeUE9Z9XHj8ZdAr001h9288Mc');

    //         // First create a product
    //         $product = \Stripe\Product::create([
    //             'name' => 'Transport Quote #' . $quote->id,
    //             'description' => "From: {$quote->pickup_locations['text']} To: {$quote->dropoff_locations['text']}",
    //         ]);

    //         // Then create a price for this product
    //         $price = \Stripe\Price::create([
    //             'product' => $product->id,
    //             'unit_amount' => (int)($quote->estimated_fare * 100),
    //             'currency' => 'gbp',
    //         ]);

    //         // Create a Payment Link with the price
    //         $paymentLink = PaymentLink::create([
    //             'line_items' => [[
    //                 'price' => $price->id,
    //                 'quantity' => 1,
    //             ]],
    //             'after_completion' => [
    //                 'type' => 'redirect',
    //                 'redirect' => [
    //                     // 'url' => route('payment.success', ['quote' => $quote->id])
    //                     'url' => 'https://www.a2blogistiks.uk'
    //                 ],
    //             ],
    //             'metadata' => [
    //                 'quote_id' => $quote->id,
    //                 'customer_id' => $quote->customer_id,
    //                 'estimated_fare' => $quote->estimated_fare,
    //             ],
    //         ]);

    //         // Update quote with payment link if the array
    //         $existingDetails = [];

    //         if (!empty($quote->payment_details)) {
    //             $existingDetails = is_array($quote->payment_details)
    //                 ? $quote->payment_details
    //                 : json_decode($quote->payment_details, true);

    //             // Fallback in case json_decode fails
    //             if (!is_array($existingDetails)) {
    //                 $existingDetails = [];
    //             }
    //         }

    //         $quote->update([
    //             'payment_details' => array_merge($existingDetails, [
    //                 'payment_link_id'   => $paymentLink->id,
    //                 'payment_link_url'  => $paymentLink->url,
    //                 'product_id'        => $product->id,
    //                 'price_id'          => $price->id,
    //                 'updated_at'        => now(),
    //             ]),
    //         ]);

    //         return [
    //             'payment_link' => $paymentLink->url,
    //             // 'quote' => $quote,
    //         ];
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function createCheckoutSession(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'quote_id' => 'required|exists:quotes,id',
            ]);

            $quote = Quote::where('payment_status', '!=', 'paid')->findOrFail($validated['quote_id']);

            \Stripe\Stripe::setApiKey(config('services.stripe.key'));

            // Calculate price with VAT
            $priceWithoutVat = $quote->estimated_fare;
            $vatAmount = $priceWithoutVat * 0.20; // Calculate 20% VAT
            $totalPrice = $priceWithoutVat + $vatAmount;

            // Create a Stripe Product
            $product = \Stripe\Product::create([
                'name' => 'Transport Quote #' . $quote->id,
                'description' => "From: {$quote->pickup_locations['text']} To: {$quote->dropoff_locations['text']}",
                'metadata' => [
                    'quote_id' => $quote->id,
                    'price_without_vat' => $priceWithoutVat,
                    'vat_amount' => $vatAmount,
                    'vat_rate' => '20%'
                ]
            ]);

            // Create a Price object with VAT included
            $price = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => (int)($totalPrice * 100),
                'currency' => 'gbp',
            ]);

            // Create Checkout Session with payment_intent_data (manual capture)
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price' => $price->id,
                    'quantity' => 1,
                ]],
                'payment_intent_data' => [
                    'capture_method' => 'manual',
                    'metadata' => [
                        'quote_id' => $quote->id,
                        'customer_id' => $quote->customer_id,
                        'price_without_vat' => $priceWithoutVat,
                        'vat_amount' => $vatAmount,
                        'total_price' => $totalPrice,
                    ],
                    'description' => "Transport Quote #" . $quote->id . " - Authorization",
                ],
                'success_url' => 'https://www.a2blogistiks.uk/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://www.a2blogistiks.uk/payment-cancel',
                'metadata' => [
                    'quote_id' => $quote->id,
                    'customer_id' => $quote->customer_id,
                ],
            ]);

            // Save session details into the quote
            $existingDetails = [];
            if (!empty($quote->payment_details)) {
                $existingDetails = is_string($quote->payment_details)
                    ? (json_decode($quote->payment_details, true) ?? [])
                    : (array) $quote->payment_details;
            }

            $quote->update([
                'payment_status' => 'pending',
                'payment_details' => array_merge($existingDetails, [
                    'session_id' => $session->id,
                    'payment_link_url' => $session->url,
                    'product_id' => $product->id,
                    'price_id' => $price->id,
                    'price_without_vat' => $priceWithoutVat,
                    'vat_amount' => $vatAmount,
                    'vat_rate' => '20%',
                    'total_price' => $totalPrice,
                    'authorized_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ]),
            ]);

            return response()->json([
                'payment_link' => $session->url,
                'quote' => $quote,
                'price_details' => [
                    'price_without_vat' => $priceWithoutVat,
                    'vat_amount' => $vatAmount,
                    'total_price' => $totalPrice,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function capturePayment($quoteId)
    {
        try {
            $quote = Quote::findOrFail($quoteId);

            if ($quote->payment_status !== 'authorised' && $quote->payment_status !== 'pending') {
                return response()->json([
                    'error' => 'Payment not authorized or already captured',
                    'status' => $quote->payment_status
                ], 400);
            }

            // Get payment details
            $paymentDetails = $quote->payment_details;
            $paymentDetails = is_string($paymentDetails) ? json_decode($paymentDetails, true) : (array)$paymentDetails;
            
            // First try to get payment_intent_id directly
            $paymentIntentId = $paymentDetails['payment_intent_id'] ?? null;
            $sessionId = $paymentDetails['session_id'] ?? null;
            
            // If payment_intent_id is not found but session_id exists, retrieve payment intent from session
            if (!$paymentIntentId && $sessionId) {
                \Stripe\Stripe::setApiKey(config('services.stripe.key'));
                
                // Get payment intent ID from session
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                $paymentIntentId = $session->payment_intent;
                
                Log::info('Retrieved payment intent from session', [
                    'session_id' => $sessionId,
                    'payment_intent_id' => $paymentIntentId
                ]);
                
                // Update payment_details with payment_intent_id
                $paymentDetails['payment_intent_id'] = $paymentIntentId;
            }

            if (!$paymentIntentId) {
                Log::error('No payment intent found', [
                    'quote_id' => $quoteId,
                    'session_id' => $sessionId
                ]);
                return response()->json(['error' => 'No payment intent found'], 404);
            }

            \Stripe\Stripe::setApiKey(config('services.stripe.key'));

            // Retrieve and capture the payment intent
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            
            // Only capture if status is requires_capture
            if ($paymentIntent->status === 'requires_capture') {
                $capturedIntent = $paymentIntent->capture();
                Log::info('Payment intent captured', [
                    'payment_intent_id' => $paymentIntentId,
                    'status' => $capturedIntent->status
                ]);
            } else {
                Log::warning('Payment intent not in capturable state', [
                    'payment_intent_id' => $paymentIntentId,
                    'current_status' => $paymentIntent->status
                ]);
                
                if ($paymentIntent->status === 'succeeded') {
                    // Payment already captured, we can still update our records
                    $capturedIntent = $paymentIntent;
                } else {
                    return response()->json([
                        'error' => 'Payment intent is not in a capturable state',
                        'status' => $paymentIntent->status
                    ], 400);
                }
            }

            // Update quote payment details
            $quote->update([
                'payment_status' => 'paid',
                'amount_paid' => $quote->estimated_fare,
                'amount_due' => 0,
                'payment_details' => array_merge($paymentDetails, [
                    'payment_intent_id' => $paymentIntentId,
                    'captured_at' => now()->toIso8601String(),
                    'payment_status' => 'paid',
                    'capture_id' => $capturedIntent->id,
                ]),
            ]);

            // Send payment confirmation
            Mail::to($quote->customer->user->email)
                ->send(new PaymentConfirmation($quote));

            return response()->json([
                'success' => true,
                'message' => 'Payment captured successfully',
                'quote' => $quote,
            ]);
        } catch (\Exception $e) {
            Log::error('Error capturing payment: ' . $e->getMessage(), [
                'quote_id' => $quoteId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        // \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        \Stripe\Stripe::setApiKey(config('services.stripe.key'));
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $endpoint_secret = 'whsec_tEVNobFhHQNySGAuYGm8WSwWfWeH5SvZ';


        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            echo '⚠️  Webhook error while parsing basic request.';
            http_response_code(400);
            exit();
        }

        try {


            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            // Log::info('Stripe webhook event received', [
            //     'type'      => $event->type,
            //     'detail'    => $event,
            //     'id'        => $event->id
            // ]);            // Handle different webhook events

        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid Stripe webhook payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid Stripe webhook signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook handling failed'], 500);
        }

        // Log::info('Stripe webhook event received', [
        //     'type'      => $event->type,
        //     'detail'    => $event,
        //     'id'        => $event->id
        // ]);

        $this->handleEvent($event);
    }


    protected function handleEvent(StripeEvent $event)
    {
        switch ($event->type) {
            // case 'payment_intent.succeeded':
            //     $paymentIntent = $event->data->object;
            //     $this->handleSuccessfulPayment($paymentIntent);
            //     break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handleFailedPayment($paymentIntent);
                break;

            // case 'payment_link.created':
            //     $paymentLink = $event->data->object;
            //     $this->handlePaymentLinkCreated($paymentLink);
            //     break;

            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;

            case 'checkout.session.expired':
                $session = $event->data->object;
                $this->handleCheckoutSessionExpired($session);
                break;

            default:
                // Unexpected event type
                Log::warning('Unhandled Stripe webhook event: ' . $event->type);
        }
    }

    private function handleSuccessfulPayment($paymentIntent)
    {

        $quote = Quote::where('payment_details->payment_intent_id', $paymentIntent->id)->first();
        Log::info('Payment intent', ['quote' => $quote]);
        if ($quote) {
            $quote->update([
                'payment_status' => 'paid',
                'amount_paid' => $paymentIntent->amount / 100,
                'amount_due' => 0,
                'payment_method' => 'stripe',
                'payment_details' => array_merge(
                    json_decode($quote->payment_details ?? '[]', true),
                    [
                        'payment_intent_id' => $paymentIntent->id,
                        'payment_method_types' => $paymentIntent->payment_method_types,
                        'payment_status' => $paymentIntent->status,
                        'payment_date' => now(),
                    ]
                )
            ]);

            // Send payment confirmation email
            Mail::to($quote->customer->user->email)
                ->send(new PaymentConfirmation($quote));
        }
    }

    private function handleFailedPayment($paymentIntent)
    {
        $quote = Quote::where('payment_details->payment_intent_id', $paymentIntent->id)->first();
        if ($quote) {
            $quote->update([
                'payment_status' => 'failed',
                'payment_details' => array_merge(
                    json_decode($quote->payment_details ?? '[]', true),
                    [
                        'payment_intent_id' => $paymentIntent->id,
                        'error_message' => $paymentIntent->last_payment_error->message ?? 'Payment failed',
                        'failed_at' => now(),
                    ]
                )
            ]);

            // Send payment failed notification
            Mail::to($quote->customer->user->email)
                ->send(new PaymentFailed($quote));
        }
    }

    private function handleCheckoutSessionCompleted($session)
    {

        $quoteId = $session['metadata']['quote_id'] ?? null;
        if (!$quoteId) {
            Log::error('Checkout session missing quote ID', ['session_id' => $session['id']]);
            return;
        }
        Log::info('Checkout session completed', ['session_id' => $session]);
        $quote = Quote::find($quoteId);


        if ($quote) {

            if (is_string($quote->payment_details)) {
                $existingPaymentDetails = json_decode($quote->payment_details, true) ?? [];
            } else {
                $existingPaymentDetails = (array) $quote->payment_details;
            }

            $quote->update([
                'payment_status' => 'paid',
                'amount_paid' => $session->amount_total / 100,
                'amount_due' => 0,
                'payment_method' => 'stripe',
                'payment_details' =>  array_merge(
                    $existingPaymentDetails,
                    [
                        'stripe_session_id'  => $session['id'],
                        'payment_intent_id'  => $session['payment_intent'],
                        'payment_status'     => $session['payment_status'],
                        'customer_email'     => $session['customer_details']['email'],
                        'payment_date'       => now(),
                    ]
                ),
            ]);

            // Send payment confirmation email
            Mail::to($quote->customer->user->email)
                ->send(new PaymentConfirmation($quote));
        }
    }

    private function handleCheckoutSessionExpired($session)
    {
        $quote = Quote::find($session->metadata->quote_id);
        if ($quote) {
            $quote->update([
                'payment_status' => 'expired',
                'payment_details' => array_merge(
                    json_decode($quote->payment_details ?? '[]', true),
                    [
                        'stripe_session_id' => $session->id,
                        'expired_at' => now(),
                    ]
                )
            ]);

            // Send payment expired notification
            Mail::to($quote->customer->user->email)
                ->send(new PaymentExpired($quote));
        }
    }

    private function handlePaymentLinkCreated($paymentLink)
    {
        Log::info('Payment link created', ['payment_link_id' => $paymentLink->id]);
    }
}
