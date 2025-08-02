<?php

namespace App\Http\Requests;

use App\Enums\StockTransferStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockTransferRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'delivery_integration_id' => 'required|exists:companies,id',
            'warehouse_from_id' => 'required|exists:warehouses,id',
            'warehouse_to_id' => 'required|exists:warehouses,id|different:warehouse_from_id',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'warehouse_to_id.different' => 'The receiving warehouse must be different from the sending warehouse.',
            'products.required' => 'At least one product must be included in the transfer.',
            'products.*.quantity.min' => 'Product quantity must be at least 1.',
        ];
    }
}
