<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(25)
            ->hasVouchers(5)
            ->create();
        
        User::factory()
            ->count(15)
            ->hasVouchers(10)
            ->create();

        User::factory()
            ->count(20)
            ->create();
    }
}
