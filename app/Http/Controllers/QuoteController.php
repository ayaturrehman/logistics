<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Mail\QuoteCreated;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;
use App\Services\FareCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Quote::with(['customer', 'vehicleType'])->latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuoteRequest $request)
    {
        try {
            // Validate the input
            $validated = $request->validated();

            // Check if customer exists based on email
            $customer = Customer::whereHas('user', function ($query) use ($validated) {
                $query->where('email', $validated['email']);
            })->first();

            // If customer does not exist, create user & customer
            if (!$customer) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('defaultpassword'), // Set a default password
                    'role' => 'driver', // Default role as driver
                ]);

                $customer = Customer::create([
                    'user_id' => $user->id, // Link to created user
                    'business_id' => getUserBusinessId(), // Assign business ID
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'country' => $validated['country'] ?? null,
                    'status' => 'active', // Default status
                ]);
            }

            // Calculate estimated fare
            $estimatedFare = FareCalculationService::calculateFare(
                $validated['vehicle_type'],
                $validated['estimated_distance']
            );

            // Create the quote
            $quote = new Quote();
            $quote->business_id             = getUserBusinessId();
            $quote->customer_id             = $customer->id;
            $quote->vehicle_type_id         = $validated['vehicle_type'];
            $quote->goods_type_id           = $validated['good_type'];
            $quote->transport_type_id       = $validated['transport_type'];
            $quote->pickup_time             = $validated['datetime'];
            $quote->pickup_locations        = $validated['pickup_locations'];
            $quote->dropoff_locations       = $validated['dropoff_locations'];
            $quote->estimated_distance      = $validated['estimated_distance'];
            $quote->estimated_fare          = $estimatedFare;
            $quote->status                  = 'pending';
            $quote->collection_place_id     = $validated['collection_place_id'] ?? null;
            $quote->collection_contact_name = $validated['collection_contact_name'] ?? null;
            $quote->collection_contact_phone = $validated['collection_contact_phone'] ?? null;
            $quote->collection_contact_email = $validated['collection_contact_email'] ?? null;
            $quote->delivery_place_id       = $validated['delivery_place_id'] ?? null;
            $quote->delivery_contact_name   = $validated['delivery_contact_name'] ?? null;
            $quote->delivery_contact_phone  = $validated['delivery_contact_phone'] ?? null;
            $quote->delivery_contact_email  = $validated['delivery_contact_email'] ?? null;
            $quote->vehicle_available_from  = $validated['vehicle_available_from'] ?? null;
            $quote->vehicle_available_to    = $validated['vehicle_available_to'] ?? null;
            $quote->vehicle_make            = $validated['vehicle_make'] ?? null;
            $quote->vehicle_model           = $validated['vehicle_model'] ?? null;
            $quote->number_plate            = $validated['number_plate'] ?? null;
            $quote->gearbox                 = $validated['gearbox'] ?? null;
            $quote->seating_capacity        = $validated['seating_capacity'] ?? null;
            $quote->comments                = $validated['comments'] ?? null;
            $quote->payment_method          = $validated['payment_method'] ?? null;
            $quote->payment_details         = $validated['payment_details'] ?? [];
            $quote->amount_paid             = $validated['amount_paid'] ?? 0.00;
            $quote->amount_due              = $validated['amount_due'] ?? 0.00;
            $quote->payment_status          = $validated['payment_status'] ?? 'pending';
            $quote->save();

            // Generate payment link
            $paymentLink = "https://demo-payment.example.com/pay/" . $quote->id;

            // Send email to customer
            Mail::to($customer->user->email)->send(new QuoteCreated($quote, $paymentLink));

            return response()->json([
                'success' => true,
                'message' => 'Quote created successfully',
                'quote' => $quote,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Quote::with(['customer.user', 'vehicleType', 'goodTypes.tranportTypes'])->findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuoteRequest $request, $id)
    {
        $quote = Quote::findOrFail($id);
        $validated = $request->validate();

        $quote->update($validated);

        return response()->json([
            'message' => 'Quote updated successfully',
            'quote' => $quote
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Quote::findOrFail($id)->delete();
        return response()->json(['message' => 'Quote deleted successfully']);
    }

    public function storewebsite(Request $request)
    {

        try {
            // Validate the input
            $validated = $request->validate([
                'name'                          => 'required|string|max:255',
                'email'                         => 'required|email|max:255',
                'phone'                         => 'required|string|max:20',
                'datetime'                      => 'nullable',
                'transport_type'                => 'nullable|exists:transport_types,id',
                'address'                       => 'nullable|string|max:255',
                'city'                          => 'nullable|string|max:100',
                'state'                         => 'nullable|string|max:100',
                'postal_code'                   => 'nullable|string|max:20',
                'country'                       => 'nullable|string|max:100',
                'vehicle_type'                  => 'required|exists:vehicle_types,id',
                'pickup_locations'              => 'required|array',
                'dropoff_locations'             => 'required|array',
                'estimated_distance'            => 'required|numeric|min:0.1',
                'collection_place_type'         => 'nullable|string|in:garage,dealership,house,auto,company,branch,shop,other',
                'collection_contact_name'       => 'nullable|string|max:255',
                'collection_contact_phone'      => 'nullable|string|max:20',
                'collection_contact_email'      => 'nullable|email|max:255',
                'delivery_place_type'           => 'nullable|string|in:garage,dealership,house,auto,company,branch,shop,other',
                'delivery_contact_name'         => 'nullable|string|max:255',
                'delivery_contact_phone'        => 'nullable|string|max:20',
                'delivery_contact_email'        => 'nullable|email|max:255',

                'vehicle_available_from'        => 'nullable|date|after_or_equal:today',
                'vehicle_available_to'          => 'nullable|date|after_or_equal:vehicle_available_from',
                'vehicle_make'                  => 'nullable|string|max:255',
                'vehicle_model'                 => 'nullable|string|max:255',
                'number_plate'                  => 'nullable|string|max:255',
                'gearbox'                       => 'nullable|string|max:255',
                'seating_capacity'              => 'nullable|integer',
                'comments'                      => 'nullable|string',
                'payment_method'                => 'nullable|string|max:255',
                'payment_details'               => 'nullable|array',
                'amount_paid'                   => 'nullable|numeric|min:0',
                'amount_due'                    => 'nullable|numeric|min:0',
                'payment_status'                => 'nullable|in:pending,paid,partially_paid,failed',
            ]);

            // Check if customer exists based on email

            $user = User::where('email', $validated['email'])->first();
            $customer = null;
            if ($user) {
                $customer = Customer::where('user_id', $user->id)->first();
            } else {
                $customer = null;
            }
            // $customer = Customer::where('user_id', $user?->id)->first();

            if (!$customer && $user) {
                $customer = Customer::create([
                    'user_id'       => $user->id, // Link to created user
                    'business_id'   => getUserBusinessId(), // Assign business ID
                    'phone'         => $validated['phone'] ?? null,
                    'address'       => $validated['address'] ?? null,
                    'city'          => $validated['city'] ?? null,
                    'state'         => $validated['state'] ?? null,
                    'postal_code'   => $validated['postal_code'] ?? null,
                    'country'       => $validated['country'] ?? null,
                    'status'        => 'active', // Default status
                ]);
            }

            $customer = Customer::whereHas('user', function ($query) use ($validated) {
                $query->where('email', $validated['email']);
            })->first();

            if (!$customer) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('defaultpassword'), // Set a default password
                    'role' => 'driver', // Default role as driver
                ]);

                $customer = Customer::create([
                    'user_id' => $user->id, // Link to created user
                    'business_id' => getUserBusinessId(), // Assign business ID
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'country' => $validated['country'] ?? null,
                    'status' => 'active', // Default status
                ]);
            }

            // Calculate estimated fare
            $estimatedFare = FareCalculationService::calculateFare(
                $validated['vehicle_type'],
                $validated['estimated_distance']
            );

            // Create the quote
            $quote = new Quote();
            $quote->business_id                 = getUserBusinessId();
            $quote->customer_id                 = $customer->id;
            $quote->vehicle_type_id             = $validated['vehicle_type'];
            // $quote->goods_type_id               = $validated['good_type'];
            $quote->transport_type_id           = $validated['transport_type'];
            $quote->pickup_time                 = $validated['datetime'];
            $quote->pickup_locations            = $validated['pickup_locations'];
            $quote->dropoff_locations           = $validated['dropoff_locations'];
            $quote->estimated_distance          = $validated['estimated_distance'];
            $quote->estimated_fare              = $estimatedFare;
            $quote->status                      = 'pending';
            $quote->collection_place_type       = $validated['collection_place_type'] ?? null;
            $quote->collection_contact_name     = $validated['collection_contact_name'] ?? null;
            $quote->collection_contact_phone    = $validated['collection_contact_phone'] ?? null;
            $quote->collection_contact_email    = $validated['collection_contact_email'] ?? null;
            $quote->delivery_place_type         = $validated['delivery_place_type'] ?? null;
            $quote->delivery_contact_name       = $validated['delivery_contact_name'] ?? null;
            $quote->delivery_contact_phone      = $validated['delivery_contact_phone'] ?? null;
            $quote->delivery_contact_email      = $validated['delivery_contact_email'] ?? null;
            $quote->vehicle_available_from      = $validated['vehicle_available_from'] ?? null;
            $quote->vehicle_available_to        = $validated['vehicle_available_to'] ?? null;
            $quote->vehicle_make                = $validated['vehicle_make'] ?? null;
            $quote->vehicle_model               = $validated['vehicle_model'] ?? null;
            $quote->number_plate                = $validated['number_plate'] ?? null;
            $quote->gearbox                     = $validated['gearbox'] ?? null;
            $quote->seating_capacity            = $validated['seating_capacity'] ?? null;
            $quote->comments                    = $validated['comments'] ?? null;
            $quote->payment_method              = $validated['payment_method'] ?? null;
            $quote->amount_paid                 = $validated['amount_paid'] ?? 0.00;
            $quote->amount_due                  = $validated['amount_due'] ?? 0.00;
            $quote->payment_status              = $validated['payment_status'] ?? 'pending';
            $quote->save();

            $stripeController = new StripePaymentController();
            $checkoutResponse = $stripeController->createCheckoutSession(new Request([
                'quote_id' => $quote->id
            ]));
            $responseData = $checkoutResponse->getData();
            $paymentLink = $responseData->payment_link ?? null;

            //   payment link
            
            Mail::to($customer->user->email)->send(new QuoteCreated($quote, $paymentLink));

            return response()->json([
                'payment_link' => $paymentLink,
                'success' => true,
                'message' => 'Quote created successfully',
                'quote' => $quote,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function capturePayment($id)
    {
        try {
            $quote = Quote::findOrFail($id);

            // Check if the status allows for payment capture
            if ($quote->status != 'collected' && $quote->status != 'in_transit') {
                return response()->json([
                    'error' => 'Vehicle must be collected before payment can be captured'
                ], 400);
            }

            // Check if payment is authorized
            if ($quote->payment_status != 'authorized') {
                return response()->json([
                    'error' => 'Payment not authorized or already captured'
                ], 400);
            }

            $stripeController = new StripePaymentController();
            $result = $stripeController->capturePayment($quote->id);

            if ($result->getData()->success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment captured successfully',
                    'quote' => Quote::with(['customer', 'vehicleType'])->find($quote->id)
                ]);
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            Log::error('Payment capture error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
