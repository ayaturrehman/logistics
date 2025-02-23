<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Services\FareCalculationService;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Quote::with(['customer', 'vehicleType'])->get());
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
        return response()->json(Quote::with(['customer', 'vehicleType'])->findOrFail($id));
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
}
