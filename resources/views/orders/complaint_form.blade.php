<form action="{{ route('complaints.store', $order->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="ly_do">Lý do khiếu nại:</label><br>
        <textarea id="ly_do" name="ly_do" rows="3" required>{{ old('ly_do') }}</textarea>
    </div>
    <div>
        <label for="hinh_anh">Ảnh minh chứng (.jpg, .png, tối đa 5 MB):</label><br>
        <input type="file" id="hinh_anh" name="hinh_anh" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
        <div id="file-error" style="color: red; margin-top: 4px;"></div>
    </div>
    <button type="submit">Gửi khiếu nại</button>
</form>

<script>
document.getElementById('hinh_anh')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const errorEl = document.getElementById('file-error');
    if (errorEl) {
        errorEl.textContent = '';
    }
    if (!file) {
        return;
    }

    const validTypes = ['image/jpeg', 'image/png'];
    const maxSize = 5 * 1024 * 1024;

    if (!validTypes.includes(file.type)) {
        if (errorEl) {
            errorEl.textContent = 'Lỗi: Chỉ chấp nhận ảnh định dạng .jpg hoặc .png!';
        }
        e.target.value = '';
        return;
    }

    if (file.size > maxSize) {
        if (errorEl) {
            errorEl.textContent = 'Lỗi: Dung lượng ảnh vượt quá 5 MB!';
        }
        e.target.value = '';
    }
});
</script>