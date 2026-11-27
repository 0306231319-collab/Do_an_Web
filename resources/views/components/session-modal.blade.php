<div
    x-data="{
        open: false,
        email: '',
        password: '',
        error: '',
        loading: false,
        async relogin() {
            this.loading = true;
            this.error = '';
            try {
                const tokenResponse = await fetch('/csrf-token', {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });
                if (!tokenResponse.ok) throw new Error('Không lấy được CSRF token.');
                const tokenData = await tokenResponse.json();
                document.querySelector('meta[name=csrf-token]').content = tokenData.token;

                const response = await fetch('/relogin', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': tokenData.token,
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: this.email, password: this.password })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Email hoặc mật khẩu không đúng.');

                if (data.token) document.querySelector('meta[name=csrf-token]').content = data.token;
                this.open = false;
                this.password = '';
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Đăng nhập lại thành công. Hãy thử lại thao tác vừa rồi.' }
                }));
            } catch (error) {
                this.error = error.message || 'Không thể đăng nhập lại.';
            } finally {
                this.loading = false;
            }
        }
    }"
    @@session-expired.window="open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="session-modal-title"
>
    <div class="w-full max-w-sm space-y-3 rounded-xl bg-white p-6 shadow-xl">
        <h2 id="session-modal-title" class="text-lg font-bold">Phiên làm việc đã hết hạn</h2>
        <p class="text-sm text-gray-600">Đăng nhập lại để tiếp tục sử dụng.</p>
        <form @submit.prevent="relogin()" class="space-y-3">
            <input x-model.trim="email" type="email" autocomplete="username" required
                   placeholder="Email" class="w-full rounded-lg border p-3">
            <input x-model="password" type="password" autocomplete="current-password" required
                   placeholder="Mật khẩu" class="w-full rounded-lg border p-3">
            <p x-show="error" x-text="error" class="text-sm text-red-600"></p>
            <button type="submit" :disabled="loading"
                    class="w-full rounded-lg bg-emerald-700 px-4 py-3 font-semibold text-white disabled:opacity-50">
                <span x-text="loading ? 'Đang đăng nhập...' : 'Đăng nhập lại'"></span>
            </button>
        </form>
        <button type="button" @click="open = false"
                class="w-full rounded-lg border px-4 py-2 text-sm">Đóng</button>
    </div>
</div>
