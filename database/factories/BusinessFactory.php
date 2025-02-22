<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition()
    {
        $owner = User::factory()->create(['role' => 'admin']);

        return [
            'name' => $this->faker->company,
            'owner_id' => $owner->id, // Assign owner user
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'is_active' => $this->faker->boolean(90), // 90% businesses active
        ];
    }
}
