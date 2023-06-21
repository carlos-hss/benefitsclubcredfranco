<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;
    
    public function definition(): array
    {
        $floatPrice = $this->faker->numberBetween(0, 15000) / 100;
        $floatWeight = $this->faker->numberBetween(10, 2000) / 100;

        $price = $floatPrice;
        $points_cost = $floatPrice * 7;
        $weight = "{$floatWeight}kg";
        $status = $this->faker->randomElement(['A', 'I']);

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->realText(100),
            'price' => $price,
            'weight' => $weight,
            'points_cost' => $points_cost,
            'status' => $status
        ];
    }
}
