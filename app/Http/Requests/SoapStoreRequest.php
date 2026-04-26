<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SoapStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // тут может быть любой тип авторизации (JWT и тд)
        $apiKey = $this->header('X-API-Key');
        Log::debug('API Key: ' . $apiKey);
        Log::debug('API Key: ' . config('app.soap_api_key'));
        return $apiKey === config('app.soap_api_key');

    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:100',
            'user_id' => 'nullable|integer|exists:users,id',
            'client_name' => 'nullable|string|max:255',
            'client_surname' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'currency' => 'nullable|string|size:3',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'locale' => 'nullable|string|size:5',

            // Массив товаров
            'items' => 'required|array|min:1',
            'items.*.amount' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.weight' => 'nullable|numeric|min:0',

            // Доставка (опционально)
            'delivery' => 'nullable|array',
            'delivery.cost' => 'nullable|numeric|min:0',
            'delivery.type' => 'nullable|integer|in:0,1',
            'delivery.country_id' => 'nullable|integer|exists:countries,id',
            'delivery.city' => 'nullable|string|max:200',
            'delivery.address' => 'nullable|string|max:300',

            // Оплата
            'payment' => 'required|array',
            'payment.type' => 'required|integer|in:1,2,3', // 1-card, 2-bank, 3-cash
            'payment.vat_type' => 'nullable|integer|in:0,1',
            'payment.vat_number' => 'nullable|string|max:100',
        ];
    }

    /**
     * Кастомные сообщения об ошибках
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email обязателен для создания заказа',
            'items.required' => 'Заказ должен содержать хотя бы один товар',
            'payment.type.required' => 'Необходимо указать тип оплаты',
        ];
    }
}
