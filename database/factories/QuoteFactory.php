<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'business_id' => Business::inRandomOrder()->first()->id ?? Business::factory(),
            'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory(),
            'vehicle_type_id' => VehicleType::inRandomOrder()->first()->id ?? VehicleType::factory(),
            'pickup_locations' => [
                [
                    'text' => $this->faker->address,
                    'latitude' => $this->faker->latitude,
                    'longitude' => $this->faker->longitude
                ]
            ],
            'stops' => $this->faker->boolean(50) ? [
                [
                    'text' => $this->faker->address,
                    'latitude' => $this->faker->latitude,
                    'longitude' => $this->faker->longitude
                ]
            ] : [],
            'dropoff_locations' => [
                [
                    'text' => $this->faker->address,
                    'latitude' => $this->faker->latitude,
                    'longitude' => $this->faker->longitude
                ]
            ],
            'estimated_distance' => $this->faker->randomFloat(2, 10, 500),
            'estimated_fare' => $this->faker->randomFloat(2, 20, 500),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
