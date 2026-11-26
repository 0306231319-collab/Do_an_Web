<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id'   => 'required|exists:products,id',
            'batch_code'   => 'required|string|max:50|unique:batches,batch_code,' . $this->route('batch'),
            'harvest_date' => 'required|date',
            'expiry_date'  => 'required|date|after_or_equal:harvest_date',
            'quantity'     => 'required|integer|min:0',
            'status'       => 'required|string',
        ];
    }
}
