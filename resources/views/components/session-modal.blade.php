<div x-data="{
        open: false, email: '', password: '', error: '', loading: false,
        async relogin() {
            this.loading = true; this.error = '';
            const t = await (await fetch('/csrf-token')).json();
            document.querySelector('meta[name=csrf-token]').content = t.token;
            const res = await fetch('/relogin', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': t.token, 'Accept': 'application/json', 'Content-Type': 'application/json'},
                body: JSON.stringify({ email: this.email, password: this.password })
            });
            this.loading = false;
            if (res.ok) {
                document.querySelector('meta[name=csrf-token]').content = (await res.json()).token;
                this.open = false;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Đăng nhập lại thành công, hãy thử lại thao tác.' } }));
            } else { this.error = 'Email hoặc mật khẩu không đúng.'; }
        }
     }"
     @@session-expired.window="open = true"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white p-6 rounded w-96 space-y-3">
        <h2 class="text-lg font-bold">Phiên đăng nhập đã hết hạn</h2>
        <p class="text-sm">Vui lòng đăng nhập lại để tiếp tục.</p>
        <input x-model="email" type="email" placeholder="Email" class="w-full border rounded p-2">
        <input x-model="password" type="password" placeholder="Mật khẩu" class="w-full border rounded p-2">
        <p x-text="error" class="text-red-600 text-sm"></p>
        <button @click="relogin()" :disabled="loading" :class="loading && 'opacity-50'"
                class="w-full bg-green-600 text-white py-2 rounded">Đăng nhập</button>
    </div>
</div>