<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $type = $this->faker->randomElement(['self_employed', 'salary_based']);

        return [
            'business_id' => Business::inRandomOrder()->first()->id ?? Business::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'type' => $type,
            'base_fee' => 25.00, // Fixed fee
            'per_mile_rate' => $type === 'self_employed' ? 1.00 : null, // Only for self-employed
            'commission_rate' => $type === 'self_employed' ? $this->faker->randomFloat(2, 5, 20) : null,
            'fixed_salary' => $type === 'salary_based' ? $this->faker->randomFloat(2, 2000, 4000) : null,
            'license_number' => strtoupper($this->faker->bothify('??#####')),
            'license_expiry' => $this->faker->dateTimeBetween('+1 year', '+5 years')->format('Y-m-d'),
            'dvla_report' => null,
            'insurance_policy_number' => $this->faker->bothify('INS-#####'),
            'insurance_expiry' => $this->faker->dateTimeBetween('+1 year', '+5 years')->format('Y-m-d'),
            'owns_vehicle' => $this->faker->boolean(50), // 50% chance of owning a vehicle
            'years_of_experience' => $this->faker->numberBetween(1, 30),
            'certifications' => $this->faker->randomElement(['CPC', 'HGV', 'None']),
            'available' => $this->faker->boolean(80), // 80% chance of being available
        ];
    }
}
