<form action="{{ route('complaints.store', $order->id) }}" method="POST" enctype="multipart/form-data"
      class="space-y-5 rounded-2xl bg-white p-6 shadow-sm border border-green-200">
    @csrf

    <div>
        <label for="ly_do" class="mb-1 block text-sm font-semibold text-gray-700">Lý do khiếu nại</label>
        <select id="ly_do" name="ly_do" required
                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">
            <option value="">-- Chọn lý do --</option>
            <option value="DapNat" @selected(old('ly_do') === 'DapNat')>Hàng bị dập nát</option>
            <option value="HuHong" @selected(old('ly_do') === 'HuHong')>Hàng bị hư hỏng</option>
            <option value="Khac" @selected(old('ly_do') === 'Khac')>Lý do khác</option>
        </select>
    </div>

    <div>
        <label for="mo_ta" class="mb-1 block text-sm font-semibold text-gray-700">Mô tả chi tiết (không bắt buộc)</label>
        <textarea id="mo_ta" name="mo_ta" rows="3" maxlength="1000"
                  class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-200"
                  placeholder="Ví dụ: 3 trong 10 quả cà chua bị dập khi nhận hàng">{{ old('mo_ta') }}</textarea>
    </div>

    <div>
        <label for="hinh_anh" class="mb-1 block text-sm font-semibold text-gray-700">
            Ảnh minh chứng <span class="font-normal text-gray-500">(.jpg, .png, tối đa 5 MB)</span>
        </label>
        <input type="file" id="hinh_anh" name="hinh_anh" required
               accept=".jpg,.jpeg,.png,image/jpeg,image/png"
               class="block w-full rounded-xl border border-dashed border-green-400 bg-green-50 p-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-green-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-green-700">
        <p id="file-error" class="mt-1 text-sm font-medium text-red-600"></p>
    </div>

    <button type="submit"
            class="w-full rounded-xl bg-green-600 px-5 py-3 font-semibold text-white transition hover:bg-green-700">
        Gửi khiếu nại
    </button>
</form>

<script>
document.getElementById('hinh_anh')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const errorEl = document.getElementById('file-error');
    if (errorEl) errorEl.textContent = '';
    if (!file) return;

    const validTypes = ['image/jpeg', 'image/png'];
    const maxSize = 5 * 1024 * 1024;

    if (!validTypes.includes(file.type)) {
        if (errorEl) errorEl.textContent = 'Lỗi: Chỉ chấp nhận ảnh định dạng .jpg hoặc .png!';
        e.target.value = '';
        return;
    }
    if (file.size > maxSize) {
        if (errorEl) errorEl.textContent = 'Lỗi: Dung lượng ảnh vượt quá 5 MB!';
        e.target.value = '';
    }
});
</script>
