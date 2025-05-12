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
    public function createCheckoutSession(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'quote_id' => 'required|exists:quotes,id',
            ]);

            // Get quote details // where status is payment_status is not paid
            $quote = Quote::where('payment_status', '!=', 'paid')->findOrFail($validated['quote_id']);

            if($quote->payment_status == 'paid'){
                return response()->json(['error' => 'Payment already made'], 400);
            }

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            // \Stripe\Stripe::setApiKey('sk_test_51R2ZOqPt6oHLLigNFVQUYoKAPItaZPXdbYHVqSru5MqOHTWO9Q97WW4C7TFd8VTYvaPLiFmMnLeUE9Z9XHj8ZdAr001h9288Mc');


            // First create a product
            $product = \Stripe\Product::create([
                'name' => 'Transport Quote #' . $quote->id,
                'description' => "From: {$quote->pickup_locations['text']} To: {$quote->dropoff_locations['text']}",
            ]);

            // Then create a price for this product
            $price = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => (int)($quote->estimated_fare * 100),
                'currency' => 'gbp',
            ]);

            // Create a Payment Link with the price
            $paymentLink = PaymentLink::create([
                'line_items' => [[
                    'price' => $price->id,
                    'quantity' => 1,
                ]],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => [
                        // 'url' => route('payment.success', ['quote' => $quote->id])
                        'url' => 'https://www.a2blogistiks.uk'
                    ],
                ],
                'metadata' => [
                    'quote_id' => $quote->id,
                    'customer_id' => $quote->customer_id,
                    'estimated_fare' => $quote->estimated_fare,
                ],
            ]);

            // Update quote with payment link if the array
            $quote->update([
                'payment_details' => array_merge(
                    $quote->payment_details ?? [],
                    [
                        'payment_link_id' => $paymentLink->id,
                        'payment_link_url' => $paymentLink->url,
                        'product_id' => $product->id,
                        'price_id' => $price->id,
                        'updated_at' => now(),
                    ]
                )
            ]);

            return [
                'payment_link' => $paymentLink->url,
                // 'quote' => $quote,
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        // \Stripe\Stripe::setApiKey('sk_test_51R2ZOqPt6oHLLigNFVQUYoKAPItaZPXdbYHVqSru5MqOHTWO9Q97WW4C7TFd8VTYvaPLiFmMnLeUE9Z9XHj8ZdAr001h9288Mc');
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
