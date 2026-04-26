<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PriceRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'factory' => 'required|string|max:255',
            'collection' => 'required|string|max:255',
            'article' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'factory.required' => 'Параметр "factory" обязателен.',
            'collection.required' => 'Параметр "collection" обязателен.',
            'article.required' => 'Параметр "article" обязателен.',
        ];
    }
}
