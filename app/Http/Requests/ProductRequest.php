<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // quyền Admin đã kiểm tra ở middleware EnsureAdmin
    }

    public function rules(): array
    {
        $id = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($id)],
            'name' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('products', 'slug')->ignore($id)],
            'unit' => ['required', 'string', 'max:30'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:base_price'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'description' => ['nullable', 'string'],
            'is_subscription' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'danh mục',
            'sku' => 'mã SKU',
            'name' => 'tên sản phẩm',
            'slug' => 'đường dẫn (slug)',
            'unit' => 'đơn vị tính',
            'base_price' => 'giá bán',
            'discount_price' => 'giá khuyến mãi',
            'thumbnail' => 'ảnh sản phẩm',
            'status' => 'trạng thái',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'unique' => ':Attribute đã tồn tại.',
            'numeric' => ':Attribute phải là số.',
            'min' => ':Attribute không được nhỏ hơn :min.',
            'discount_price.lte' => 'Giá khuyến mãi không được lớn hơn giá bán.',
            'thumbnail.image' => 'Tệp tải lên phải là ảnh.',
            'thumbnail.mimes' => 'Ảnh chỉ nhận định dạng JPG hoặc PNG.',
            'thumbnail.max' => 'Ảnh không được vượt quá 5 MB.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
        ];
    }
}
