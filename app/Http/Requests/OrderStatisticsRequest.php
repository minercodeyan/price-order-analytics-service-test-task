<?php


namespace App\Http\Requests;

class OrderStatisticsRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'group_by' => 'string|in:day,month,year',
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => 'Параметр "page" должен быть целым числом.',
            'page.min' => 'Параметр "page" должен быть не менее 1.',
            'per_page.integer' => 'Параметр "per_page" должен быть целым числом.',
            'per_page.min' => 'Параметр "per_page" должен быть не менее 1.',
            'per_page.max' => 'Параметр "per_page" не может быть больше 100.',
            'group_by.in' => 'Параметр "group_by" должен быть: day, month, year.',
        ];
    }
}
