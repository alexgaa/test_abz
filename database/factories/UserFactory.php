<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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
        $name = fake()->firstName;
        $color = rand(1, 999999);
        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+3809' . rand(10000000, 99999999),
            'position_id' => rand(1, 10),
            'photo' => "https://via.placeholder.com/70x70.jpg/" . $color . "?text=" . $name
        ];
    }

}
