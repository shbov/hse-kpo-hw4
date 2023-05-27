<?php

namespace App\Http\Requests;

use App\Rules\DishValid;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // зачем передавать статус при создании заказа? Ставим по умолчанию "created"
            // "status" => ['required', 'string'],
            "special_requests" => ['nullable', 'string'],
            'dishes' => ['required', 'array'],
            'dishes.*' => [new DishValid],
        ];
    }
}
