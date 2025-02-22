<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Fare;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fare>
 */
class FareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Fare::class;

    public function definition()
    {
        return [
            'business_id' => Business::inRandomOrder()->first()->id ?? Business::factory(),
            'vehicle_type_id' => VehicleType::inRandomOrder()->first()->id ?? VehicleType::factory(),
            'base_fare' => $this->faker->randomFloat(2, 10, 100),
            'per_mile_rate' => $this->faker->randomFloat(2, 0.50, 5.00),
        ];
    }
}
