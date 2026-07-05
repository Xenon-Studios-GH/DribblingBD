<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:2000',
            'city' => 'required|string|max:100',
            'products' => 'required|json',
            'dtf_name' => 'nullable|string|max:255',
            'dtf_number' => 'nullable|string|max:255',
            'patch_price' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'advanced_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bkash,nagad,rocket,cod,cash',
            'delivery_charge' => 'nullable|numeric|min:0',
            'status' => 'required|in:on_hold,out_of_stock',
            'notes' => 'nullable|string|max:5000',
        ];
    }
}
