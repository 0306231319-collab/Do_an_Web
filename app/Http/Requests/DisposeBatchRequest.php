<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisposeBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_id' => [
                'required',
                'exists:batches,id',
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'reason' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'batch_id.required' => 'Vui lòng chọn lô hàng.',
            'batch_id.exists' => 'Lô hàng không tồn tại.',

            'quantity.required' => 'Vui lòng nhập số lượng hủy.',
            'quantity.numeric' => 'Số lượng phải là số.',
            'quantity.gt' => 'Số lượng phải lớn hơn 0.',

            'reason.required' => 'Vui lòng nhập lý do xuất hủy.',
            'reason.min' => 'Lý do phải có ít nhất 3 ký tự.',
            'reason.max' => 'Lý do không được quá 255 ký tự.',

            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Chỉ chấp nhận ảnh JPG hoặc PNG.',
            'image.max' => 'Ảnh không được vượt quá 5MB.',
        ];
    }
}