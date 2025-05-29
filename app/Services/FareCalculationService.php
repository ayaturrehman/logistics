<?php

namespace App\Services;

use App\Models\Fare;

class FareCalculationService
{
    /**
     * Calculate total fare for a vehicle type based on miles.
     *
     * @param int $vehicleTypeId
     * @param float $distanceMiles
     * @return float
     */
    public static function calculateFare($vehicleTypeId, $distanceMiles)
    {
       $fare = Fare::where('vehicle_type_id', $vehicleTypeId)->first();

        if (!$fare) {
            return response()->json(['error' => 'Fare not found for this vehicle type'], 404);
        }

        $baseFare = $fare->base_fare;

        if ($distanceMiles <= 50) {
            return round($baseFare, 2);
        }

        $additionalMiles = $distanceMiles - 50;
        $additionalFare = $additionalMiles * $fare->per_mile_rate;

        $totalFare = $baseFare + $additionalFare;


        // $totalFare = $fare->base_fare + ($distanceMiles * $fare->per_mile_rate);

        return round($totalFare, 2);
    }
}
