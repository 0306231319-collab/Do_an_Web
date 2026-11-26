<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DisposeBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer|gt:0',
            'reason'   => 'required|string|max:255',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
