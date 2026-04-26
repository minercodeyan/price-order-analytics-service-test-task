<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hash' => $this->hash,
            'number' => $this->number,
            'status' => $this->status,
            'status_text' => $this->getStatusText(),
            'client_name' => $this->client_name,
            'client_surname' => $this->client_surname,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'discount' => $this->discount,
            'create_date' => $this->create_date?->toIso8601String(),
            'delivery' => $this->whenLoaded('delivery', function () {
                return [
                    'cost' => $this->delivery->delivery_cost,
                    'city' => $this->delivery->city,
                    'address' => $this->delivery->address_line,
                    'tracking_number' => $this->delivery->tracking_number,
                ];
            }),
            'payment' => $this->whenLoaded('payment', function () {
                return [
                    'type' => $this->payment->pay_type,
                    'full_payment_date' => $this->payment->full_payment_date,
                ];
            }),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }

    private function getStatusText(): string
    {
        return match ($this->status) {
            1 => 'Новый',
            2 => 'Оплачен',
            3 => 'Собран',
            4 => 'Отправлен',
            5 => 'Доставлен',
            6 => 'Отменен',
            default => 'Неизвестно',
        };
    }
}
