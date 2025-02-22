<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFareRequest;
use App\Http\Requests\UpdateFareRequest;
use App\Models\Fare;
use App\Models\VehicleType;

class FareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Fare::with('vehicleType')->get());
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
    public function store(StoreFareRequest $request)
    {
        $fare = Fare::create([
            'vehicle_type_id'   => '',
            'base_fare'         => $request->base_fare,
            'per_mile_rate'     => $request->per_mile_rate,
        ]);

        return response()->json([
            'message' => 'Fare created successfully',
            'fare' => $fare
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Fare::with('vehicleType')->findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fare $fare)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFareRequest $request, $id)
    {
        $fare = Fare::where('vehicle_type_id', $id)->first();

        if ($fare) {
            // Update existing fare
            $fare->update($request->validated());

            return response()->json([
                'message' => 'Fare updated successfully',
                'fare' => $fare
            ]);
        } else {
            // Create a new fare if it does not exist
            $fare = Fare::create([
                'business_id'       => getUserBusinessId(),
                'vehicle_type_id'   => $id,
                'base_fare'         => $request->base_fare,
                'per_mile_rate'     => $request->per_mile_rate,
            ]);

            return response()->json([
                'message' => 'Fare created successfully',
                'fare' => $fare
            ], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Fare::findOrFail($id)->delete();
        return response()->json(['message' => 'Fare deleted successfully']);
    }
}
