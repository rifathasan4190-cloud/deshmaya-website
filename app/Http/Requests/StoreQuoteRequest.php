<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreQuoteRequest extends SubmissionFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:180'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'product_id' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')
                    ->whereNull('deleted_at')
                    ->where('is_active', true),
            ],
            'quantity' => ['required', 'numeric', 'decimal:0,3', 'gt:0', 'max:999999999.999'],
            'unit' => ['required', Rule::in(['kg', 'tonnes', 'cartons', 'containers'])],
            'message' => ['nullable', 'string', 'max:5000'],
            ...$this->turnstileRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Please complete the security check.',
            'product_id.exists' => 'Please select an available product.',
        ];
    }
}
