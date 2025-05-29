<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleTypeRequest;
use App\Http\Requests\UpdateVehicleTypeRequest;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(VehicleType::with('fare')->get());
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
    public function store(StoreVehicleTypeRequest $request)
    {

        $vehicleType = VehicleType::create([
            'business_id'   => getUserBusinessId(),
            'name'          => $request->name,
            'description'   => $request->description,
        ]);

        return response()->json([
            'message' => 'Vehicle Type created successfully',
            'vehicle_type' => $vehicleType
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(VehicleType::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleType $vehicleType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleTypeRequest $request, $id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        $vehicleType->update($request->validated());

        return response()->json([
            'message' => 'Vehicle Type updated successfully',
            'vehicle_type' => $vehicleType
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        VehicleType::findOrFail($id)->delete();
        return response()->json(['message' => 'Vehicle Type deleted successfully']);
    }


    public function getQuotes(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'pickup_location' => 'required|string',
            // 'dropoff_location' => 'required|string',
            'estimated_distance' => 'required|numeric|min:0',
            // 'minutes' => 'required|numeric|min:0',
        ]);

        $vehicleTypes = VehicleType::with('fare')->get();

        $quotes = [];




        foreach ($vehicleTypes as $vehicle) {

            $baseFare = $vehicle->fare->base_fare;
            $distanceMiles = $request->estimated_distance;
            $additionalFare = 0;

            if ($distanceMiles > 50) {
                $additionalMiles = $distanceMiles - 50;
                $additionalFare = $additionalMiles * $vehicle->fare->per_mile_rate;
            }

            $totalFare = $baseFare + $additionalFare;

            if ($vehicle->fare && ($vehicle->fare->base_fare > 0 || $vehicle->fare->per_mile_rate > 0)) {

                $quotes[] = [
                    'id' => $vehicle->id,
                    'per_mile_rate' => $vehicle->fare->per_mile_rate,
                    'base_fare' => $vehicle->fare->base_fare,
                    'vehicle_type' => $vehicle->name,
                    'total_fare' => round($totalFare, 2),
                ];
            }
        }

        return response()->json(['quotes' => $quotes]);


        return response()->json(['quotes' => $quotes], 200);
    }
}
