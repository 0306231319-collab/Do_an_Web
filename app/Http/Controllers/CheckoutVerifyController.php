<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CheckoutVerifyController extends Controller
{
    /**
     * Tuần 7 (Bảo mật): Tính lại tổng tiền hoàn toàn từ Database ở Backend.
     * Bỏ qua mọi giá trị price/total giả mạo nếu người dùng sửa bằng F12.
     */
    public function verifyCartTotal(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.1',
        ]);

        $verifiedItems = [];
        $serverTotal = 0;

        foreach ($validated['items'] as $item) {
            // Truy vấn trực tiếp sản phẩm từ DB để lấy giá gốc chuẩn xác
            $product = Product::findOrFail($item['product_id']);
            $realPrice = (float) ($product->discount_price ?? $product->base_price);
            $qty = (float) $item['quantity'];
            $subtotal = round($realPrice * $qty, 2);

            $serverTotal += $subtotal;

            $verifiedItems[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'real_price'   => $realPrice,
                'quantity'     => $qty,
                'subtotal'     => $subtotal,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xác thực đơn giá từ cơ sở dữ liệu.',
            'data'    => [
                'items'        => $verifiedItems,
                'server_total' => round($serverTotal, 2),
            ],
        ]);
    }
}