@php $isEdit = $product->exists; @endphp
<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
      class="grid gap-4 max-w-2xl rounded-xl border border-stone-200 bg-white p-5">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <label class="grid gap-1 text-sm font-semibold">Danh mục
        <select name="category_id" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal" required>
            <option value="">-- Chọn danh mục --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </label>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="grid gap-1 text-sm font-semibold">Mã SKU
            <input name="sku" value="{{ old('sku', $product->sku) }}" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal" required>
        </label>
        <label class="grid gap-1 text-sm font-semibold">Đơn vị tính
            <input name="unit" value="{{ old('unit', $product->unit) }}" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal" required>
        </label>
    </div>

    <label class="grid gap-1 text-sm font-semibold">Tên sản phẩm
        <input name="name" value="{{ old('name', $product->name) }}" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal" required>
    </label>

    <label class="grid gap-1 text-sm font-semibold">Đường dẫn (slug), để trống sẽ tự tạo từ tên
        <input name="slug" value="{{ old('slug', $product->slug) }}" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal">
    </label>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="grid gap-1 text-sm font-semibold">Giá bán (VNĐ)
            <input type="number" min="0" step="1000" name="base_price" value="{{ old('base_price', $product->base_price !== null ? (int) $product->base_price : '') }}" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal" required>
        </label>
        <label class="grid gap-1 text-sm font-semibold">Giá khuyến mãi (VNĐ, nếu có)
            <input type="number" min="0" step="1000" name="discount_price" value="{{ old('discount_price', $product->discount_price !== null ? (int) $product->discount_price : '') }}" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal">
        </label>
    </div>

    <label class="grid gap-1 text-sm font-semibold">Mô tả
        <textarea name="description" rows="3" class="rounded-lg border border-stone-300 px-3 py-2 font-normal">{{ old('description', $product->description) }}</textarea>
    </label>

    <div class="grid gap-1 text-sm font-semibold">Ảnh sản phẩm (JPG hoặc PNG, tối đa 5 MB)
        @if ($isEdit && $product->thumbnail_url)
            <img src="{{ $product->thumbnail_url }}" alt="" class="h-24 w-24 rounded-lg border border-stone-200 object-cover" onerror="this.style.display='none'">
        @endif
        <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="font-normal"
               onchange="if(this.files[0] && this.files[0].size > 5*1024*1024){ alert('Ảnh vượt quá 5 MB.'); this.value=''; }">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="grid gap-1 text-sm font-semibold">Trạng thái
            <select name="status" class="min-h-11 rounded-lg border border-stone-300 px-3 font-normal">
                <option value="active" @selected(old('status', $product->status) === 'active')>Đang bán</option>
                <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Ngừng bán</option>
            </select>
        </label>
        <label class="flex items-center gap-2 text-sm font-semibold sm:pt-6">
            <input type="checkbox" name="is_subscription" value="1" class="h-5 w-5" @checked(old('is_subscription', $product->is_subscription))>
            Cho phép đặt theo gói định kỳ
        </label>
    </div>

    <div class="flex gap-2">
        <button class="min-h-11 rounded-lg bg-emerald-700 px-6 font-semibold text-white hover:bg-emerald-800">{{ $isEdit ? 'Lưu thay đổi' : 'Thêm sản phẩm' }}</button>
        <a href="{{ route('admin.products.index') }}" class="flex min-h-11 items-center rounded-lg border border-stone-300 px-6">Hủy</a>
    </div>
</form>
