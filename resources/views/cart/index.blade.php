
@extends('layouts.app')

@section('title', 'Giỏ hàng - Nông Sản Sạch')

@section('content')
<x-container class="py-10">
    <div
        x-data="{
            items: [],
            total: 0,
            loading: true,
            error: '',

            formatPrice(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            },

            async loadCart() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await window.apiFetch('/api/cart');
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Không thể tải giỏ hàng.');
                    }

                    this.items = result.data.items || [];
                    this.total = Number(result.data.total_amount || 0);
                } catch (error) {
                    this.error = error.message;
                } finally {
                    this.loading = false;
                }
            },

            async updateQuantity(item, quantity) {
                quantity = Number(quantity);

                if (!Number.isInteger(quantity) || quantity < 1 || quantity > 99) {
                    this.loadCart();
                    return;
                }

                try {
                    const response = await window.apiFetch('/api/cart/' + item.product_id, {
                        method: 'PUT',
                        body: JSON.stringify({ quantity })
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Không thể cập nhật số lượng.');
                    }

                    this.items = result.data.items || [];
                    this.total = Number(result.data.total_amount || 0);
                } catch (error) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: error.message }
                    }));
                    this.loadCart();
                }
            },

            async removeItem(item) {
                if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

                try {
                    const response = await window.apiFetch('/api/cart/' + item.product_id, {
                        method: 'DELETE'
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Không thể xóa sản phẩm.');
                    }

                    this.items = result.data.items || [];
                    this.total = Number(result.data.total_amount || 0);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Đã xóa sản phẩm khỏi giỏ hàng.' }
                    }));
                } catch (error) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: error.message }
                    }));
                }
            }
        }"
        x-init="loadCart()"
    >
        <h1 class="mb-6 text-3xl font-bold">Giỏ hàng của bạn</h1>

        <div x-show="loading" class="py-10 text-center text-gray-500">
            Đang tải giỏ hàng...
        </div>

        <div x-show="error" x-text="error" class="mb-4 text-red-600"></div>

        <template x-if="!loading && items.length === 0">
            <div class="rounded-xl bg-white p-10 text-center shadow-sm">
                <p class="mb-4 text-gray-600">Giỏ hàng của bạn đang trống.</p>
                <a href="/" class="rounded-lg bg-green-700 px-5 py-3 text-white">
                    Tiếp tục mua sắm
                </a>
            </div>
        </template>

        <div x-show="!loading && items.length > 0">
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
                <table class="w-full min-w-[650px]">
                    <thead class="border-b bg-gray-50">
                        <tr class="text-left">
                            <th class="p-4">Sản phẩm</th>
                            <th class="p-4">Đơn giá</th>
                            <th class="p-4">Số lượng</th>
                            <th class="p-4">Thành tiền</th>
                            <th class="p-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="item in items" :key="item.product_id">
                            <tr class="border-b">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img
                                            :src="item.thumbnail_url || ''"
                                            :alt="item.product_name"
                                            class="h-16 w-16 rounded-lg object-cover"
                                        >
                                        <div>
                                            <p class="font-semibold" x-text="item.product_name"></p>
                                            <p class="text-sm text-gray-500" x-text="item.unit"></p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4" x-text="formatPrice(item.unit_price)"></td>

                                <td class="p-4">
                                    <input
                                        type="number"
                                        min="1"
                                        max="99"
                                        :value="item.quantity"
                                        @change="updateQuantity(item, $event.target.value)"
                                        class="w-20 rounded-lg border p-2"
                                    >
                                </td>

                                <td class="p-4 font-semibold"
                                    x-text="formatPrice(item.subtotal)"></td>

                                <td class="p-4">
                                    <button
                                        type="button"
                                        @click="removeItem(item)"
                                        class="text-red-600 hover:underline"
                                    >
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col items-end gap-4">
                <p class="text-xl font-bold">
                    Tổng tiền:
                    <span class="text-green-700" x-text="formatPrice(total)"></span>
                </p>

                <a
                    href="{{ route('cart.checkout') }}"
                    class="rounded-lg bg-green-700 px-6 py-3 font-semibold text-white"
                >
                    Tiến hành thanh toán
                </a>
            </div>
        </div>
    </div>
</x-container>
@endsection
