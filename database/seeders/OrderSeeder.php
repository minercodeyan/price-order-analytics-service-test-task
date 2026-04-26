<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::factory()
            ->count(60)
            ->year(2023)
            ->create();

        Order::factory()
            ->count(80)
            ->year(2024)
            ->create();

        Order::factory()
            ->count(40)
            ->year(2025)
            ->create();

        Order::factory()
            ->count(20)
            ->delivered()
            ->create();

        Order::factory()
            ->count(10)
            ->cancelled()
            ->create();

        // Создаем заказы с разными суммами
        Order::factory()
            ->count(30)
            ->state(function (array $attributes) {
                return [
                    'total_amount' => fake()->randomFloat(2, 1000, 10000),
                ];
            })
            ->create();

        Order::factory()
            ->count(15)
            ->month(2025, 1)
            ->create();

        Order::factory()
            ->count(25)
            ->month(2025, 2)
            ->create();

        Order::factory()
            ->count(20)
            ->month(2025, 3)
            ->create();

        Order::factory()
            ->count(18)
            ->month(2025, 4)
            ->create();
    }
}
