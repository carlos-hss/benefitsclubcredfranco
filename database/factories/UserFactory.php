<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $type_user = $this->faker->randomElement(['C', 'M']);
        $status = $this->faker->randomElement(['A', 'I']);
        $points = ($type_user === "M") ? null : $this->faker->numberBetween(0, 10000);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'type_user' => $type_user,
            'points' => $points,
            'status' => $status
        ];
    }
}
