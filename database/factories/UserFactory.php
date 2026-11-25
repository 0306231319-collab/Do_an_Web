<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'phone' => '09' . fake()->unique()->numerify('########'),
            'role' => 'customer',
            'loyalty_points' => 0,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
