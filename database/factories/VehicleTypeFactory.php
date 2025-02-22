<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleType>
 */
class VehicleTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VehicleType::class;

    public function definition()
    {
        return [
            'business_id' => Business::inRandomOrder()->first()->id ?? Business::factory(),
            'name' => $this->faker->randomElement(['Car', 'Van', 'Truck', 'Motorbike']),
            'description' => $this->faker->sentence,
        ];
    }
}
