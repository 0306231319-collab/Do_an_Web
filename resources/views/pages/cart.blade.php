
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10">
    <h1 class="mb-6 text-3xl font-bold">Giỏ hàng của bạn</h1>

    <div id="cart-items" class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-gray-500">Đang tải giỏ hàng...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('cart-items');

    try {
        const response = await fetch('/api/cart', {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error('Không thể tải giỏ hàng');

        const data = await response.json();

        container.innerHTML = `
            <pre class="overflow-auto text-sm">${JSON.stringify(data, null, 2)}</pre>
        `;
    } catch (error) {
        container.innerHTML = `
            <p class="text-red-600">${error.message}</p>
        `;
    }
});
</script>
@endsection
