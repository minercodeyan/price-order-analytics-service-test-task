<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SoapService
{
    public function createSoapOrder(array $data): array
    {
        return DB::transaction(function () use ($data) {
            try {
                $order = Order::create([
                    'hash' => md5(uniqid(Str::random(10), true)),
                    'email' => $data['email'],
                    'token' => Str::random(64),
                    'total_amount' => $data['total_amount'],
                    'number' => $this->generateOrderNumber(),
                    'status' => 1,
                    'currency' => $data['currency'] ?? 'EUR',
                    'create_date' => now(),
                ]);

                foreach ($data['items'] as $item) {
                    $order->items()->create([
                        'amount' => $item['amount'],
                        'price' => $item['price'],
                        'weight' => $item['weight'] ?? 0,
                    ]);
                }

                if (!empty($data['delivery'])) {
                    $order->delivery()->create([
                        'delivery_cost' => $data['delivery']['cost'] ?? null,
                        'city' => $data['delivery']['city'] ?? null,
                        'address_line' => $data['delivery']['address'] ?? null,
                    ]);
                }

                $order->payment()->create([
                    'pay_type' => $data['payment']['type'],
                    'vat_type' => $data['payment']['vat_type'] ?? 0,
                ]);

                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->number,
                ];

            } catch (\Exception $e) {
                Log::error('SOAP Order creation failed: ' . $e->getMessage(), [
                    'data' => $data,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = date('ymd');
        $number = $prefix . mt_rand(1000, 9999);

        while (Order::where('number', $number)->exists()) {
            $number = $prefix . mt_rand(1000, 9999);
        }
        return $number;
    }
}
