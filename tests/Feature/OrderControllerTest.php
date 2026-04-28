<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_order_when_exists()
    {
        $mockService = Mockery::mock(OrderService::class);
        $mockService->shouldReceive('getOrderById')
            ->once()
            ->with(1)
            ->andReturn((object)[
                'id' => 1,
                'number' => 'ORD-001'
            ]);

        $this->app->instance(OrderService::class, $mockService);

        $response = $this->getJson('/api/orders/1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    // добавь поля из OrderResource
                ]
            ]);
    }

    public function test_show_returns_404_when_not_found()
    {
        $mockService = Mockery::mock(OrderService::class);
        $mockService->shouldReceive('getOrderById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->app->instance(OrderService::class, $mockService);

        $response = $this->getJson('/api/orders/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Заказ не найден'
            ]);
    }
}
