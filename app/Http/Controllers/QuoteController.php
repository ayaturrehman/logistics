<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;
use App\Services\FareCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $validated = $request->validated();
        $estimatedFare = FareCalculationService::calculateFare($validated['vehicle_type'], $validated['estimated_distance']);
        $quote = new Quote();

        $quote->business_id             = getUserBusinessId();
        $quote->customer_id             = $request->customer_id;
        $quote->vehicle_type_id         = $request->vehicle_type;
        $quote->pickup_locations        = $request->pickup_location;
        // $quote->stops                   = $request;
        $quote->dropoff_locations       = $request->dropoff_location;
        $quote->estimated_distance      = $request->estimated_distance;
        $quote->estimated_fare          = $estimatedFare;
        $quote->status                  = 'pending';
        $quote->save();

        return response()->json([
            'message' => 'Quote created successfully',
            'quote' => $quote
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Quote::with(['customer.user', 'vehicleType','goodTypes.tranportTypes'])->findOrFail($id));
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
                'name'                  => 'required|string|max:255',
                'email'                 => 'required|email|max:255',
                'phone'                 => 'required|string|max:20',
                'good_type'             => 'required|exists:goods_types,id',
                'transport_type'        => 'nullable|exists:transport_types,id',
                'address'               => 'nullable|string|max:255',
                'city'                  => 'nullable|string|max:100',
                'state'                 => 'nullable|string|max:100',
                'postal_code'           => 'nullable|string|max:20',
                'country'               => 'nullable|string|max:100',
                'vehicle_type'          => 'required|exists:vehicle_types,id',
                'pickup_locations'      => 'required|array',
                'dropoff_locations'     => 'required|array',
                'estimated_distance'    => 'required|numeric|min:0.1',
            ]);

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
            $quote->business_id         = getUserBusinessId();
            $quote->customer_id         = $customer->id;
            $quote->vehicle_type_id     = $validated['vehicle_type'];
            $quote->pickup_locations    = $validated['pickup_locations'];
            $quote->dropoff_locations   = $validated['dropoff_locations'];
            $quote->estimated_distance  = $validated['estimated_distance'];
            $quote->estimated_fare      = $estimatedFare;
            $quote->status              = 'pending';
            $quote->save();

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
}
