<?php
// database/factories/OrderFactory.php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Генерируем случайную дату за последние 3 года
        $createdAt = $this->faker->dateTimeBetween('-3 years', 'now');

        return [
            'hash' => md5($this->faker->unique()->uuid()),
            'user_id' => 1,
            'token' => Str::random(64),
            'number' => 'ORD-' . $this->faker->unique()->numberBetween(10000, 99999),

            'status' => $this->faker->numberBetween(1, 6),
            'step' => $this->faker->numberBetween(1, 4),

            'client_name' => $this->faker->firstName(),
            'client_surname' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'company_name' => $this->faker->boolean(40) ? $this->faker->company() : null,

            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP']),
            'cur_rate' => $this->faker->randomFloat(4, 0.85, 1.15),
            'total_amount' => $this->faker->randomFloat(2, 50, 5000),
            'discount' => $this->faker->boolean(30) ? $this->faker->numberBetween(5, 30) : null,

            'create_date' => $createdAt,
            'update_date' => $this->faker->dateTimeBetween($createdAt, 'now'),
            'deleted_at' => $this->faker->boolean(5) ? $this->faker->dateTimeBetween($createdAt, 'now') : null,
        ];
    }

    // Состояние для оплаченных заказов
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }

    // Состояние для доставленных заказов
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 5,
        ]);
    }

    // Состояние для отмененных заказов
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 6,
        ]);
    }

    // Состояние для заказов конкретного года
    public function year(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'create_date' => $this->faker->dateTimeBetween("{$year}-01-01", "{$year}-12-31"),
        ]);
    }

    // Состояние для заказов конкретного месяца
    public function month(int $year, int $month): static
    {
        $start = "{$year}-{$month}-01";
        $end = date('Y-m-t', strtotime($start));
        return $this->state(fn (array $attributes) => [
            'create_date' => $this->faker->dateTimeBetween($start, $end),
        ]);
    }
}
