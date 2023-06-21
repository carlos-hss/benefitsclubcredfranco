<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    public function definition(): array
    {
        $startUsedDate = Carbon::now()->subYear();
        $endUsedDate = Carbon::now();

        $status = $this->faker->randomElement(['C', 'U']);
        $used_date = ($status === 'U') ? $this->faker->dateTimeBetween($startUsedDate, $endUsedDate) : null;
        
        return [
            'code' => $this->faker->unique()->regexify('[A-Z0-9]{5}'),
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'used_date' => $used_date,
            'status' => $status
        ];
    }
}
