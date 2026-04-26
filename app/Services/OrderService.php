<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{
    public function getOrderById(int $id): ?Order
    {
        try {
            return Order::with(['delivery', 'payment', 'items'])
                ->whereNull('deleted_at')
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}
