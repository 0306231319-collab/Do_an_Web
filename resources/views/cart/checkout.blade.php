
@extends('layouts.app')

@section('title', 'Thanh toán COD - Nông Sản Sạch')

@section('content')
<x-container class="py-10">
    <div
        class="mx-auto max-w-2xl rounded-xl bg-white p-6 shadow-sm"
        x-data="{
            nguoi_nhan: '',
            sdt_nhan: '',
            dia_chi_giao: '',
            ghi_chu: '',
            items: [],
            total: 0,
            loading: false,
            error: '',

            formatPrice(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            },

            async loadCartData() {
                try {
                    const response = await window.apiFetch('/api/cart');
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Không thể tải giỏ hàng.');
                    }

                    this.items = result.data.items || [];
                    this.total = Number(result.data.total_amount || 0);

                    if (this.items.length === 0) {
                        window.location.href = '/cart';
                    }
                } catch (error) {
                    this.error = error.message;
                }
            },

            async submitOrder() {
                if (this.loading || this.items.length === 0) return;

                this.loading = true;
                this.error = '';

                try {
                    const response = await window.apiFetch('/api/orders', {
                        method: 'POST',
                        body: JSON.stringify({
                            nguoi_nhan: this.nguoi_nhan,
                            sdt_nhan: this.sdt_nhan,
                            dia_chi_giao: this.dia_chi_giao,
                            ghi_chu: this.ghi_chu,
                            items: this.items.map(item => ({
                                product_id: item.product_id,
                                quantity: item.quantity
                            }))
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Đặt hàng thất bại.');
                    }

                    const orderCode = result.data?.ma_don_hang || '';

                    window.location.href =
                        '/cart/thank-you?order=' + encodeURIComponent(orderCode);

                } catch (error) {
                    this.error = error.message || 'Có lỗi xảy ra khi đặt hàng.';
                } finally {
                    this.loading = false;
                }
            }
        }"
        x-init="loadCartData()"
    >
        <h1 class="mb-6 text-2xl font-bold">Thông tin giao hàng</h1>

        <form @submit.prevent="submitOrder()" class="space-y-4">
            <div>
                <label class="mb-1 block font-medium">Tên người nhận</label>
                <input
                    type="text"
                    x-model.trim="nguoi_nhan"
                    required
                    maxlength="255"
                    class="w-full rounded-lg border p-3"
                    placeholder="Nhập tên người nhận"
                >
            </div>

            <div>
                <label class="mb-1 block font-medium">Số điện thoại</label>
                <input
                    type="tel"
                    x-model.trim="sdt_nhan"
                    required
                    maxlength="20"
                    class="w-full rounded-lg border p-3"
                    placeholder="Nhập số điện thoại"
                >
            </div>

            <div>
                <label class="mb-1 block font-medium">Địa chỉ giao hàng</label>
                <textarea
                    x-model.trim="dia_chi_giao"
                    required
                    maxlength="500"
                    rows="3"
                    class="w-full rounded-lg border p-3"
                    placeholder="Nhập địa chỉ giao hàng"
                ></textarea>
            </div>

            <div>
                <label class="mb-1 block font-medium">Ghi chú</label>
                <textarea
                    x-model.trim="ghi_chu"
                    maxlength="500"
                    rows="2"
                    class="w-full rounded-lg border p-3"
                    placeholder="Ghi chú cho người bán (không bắt buộc)"
                ></textarea>
            </div>

            <div class="rounded-lg bg-gray-50 p-4">
                <div class="mb-2 flex justify-between">
                    <span>Phương thức thanh toán</span>
                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                </div>

                <div class="flex justify-between border-t pt-3 text-lg font-bold">
                    <span>Tổng tiền</span>
                    <span class="text-green-700" x-text="formatPrice(total)"></span>
                </div>
            </div>

            <p x-show="error" x-text="error" class="text-sm text-red-600"></p>

            <button
                type="submit"
                :disabled="loading || items.length === 0"
                class="w-full rounded-lg bg-green-700 px-5 py-3 font-semibold text-white disabled:opacity-50"
            >
                <span x-text="loading ? 'Đang đặt hàng...' : 'Xác nhận đặt hàng COD'"></span>
            </button>

            <a href="/cart" class="block text-center text-sm text-gray-600 hover:underline">
                Quay lại giỏ hàng
            </a>
        </form>
    </div>
</x-container>
@endsection
