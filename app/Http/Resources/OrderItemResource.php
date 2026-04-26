<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'article_id' => $this->article_id,
            'article_sku' => $this->article_sku,
            'amount' => $this->amount,
            'price' => $this->price,
            'price_eur' => $this->price_eur,
            'currency' => $this->currency,
        ];
    }
}
