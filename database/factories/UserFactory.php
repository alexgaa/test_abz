<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+3809' . rand(10000000, 99999999),
            'position_id' => rand(1, 10),
            'photo' => fake()->imageUrl, //TODO исправить значение
            'password' => fake()->password(10,10),
            'remember_token' => Str::random(50),
        ];
    }

}
