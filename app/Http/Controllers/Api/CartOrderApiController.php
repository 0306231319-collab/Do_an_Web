<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CartOrderApiController extends Controller
{
    public function getCart(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $price = (float) ($product->price ?? $product->don_gia ?? 0);
                $item['unit_price'] = $price;
                $item['subtotal']   = $price * (int) $item['quantity'];
                $total += $item['subtotal'];
            }
        }
        unset($item);

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Lấy giỏ hàng thành công.',
            'data'    => [
                'items'        => array_values($cart),
                'total_amount' => $total,
            ],
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $price = (float) ($product->price ?? $product->don_gia ?? 0);
        $cart = session()->get('cart', []);
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += (int) $validated['quantity'];
            $cart[$id]['unit_price'] = $price;
        } else {
            $cart[$id] = [
                'product_id'   => $product->id,
                'product_name' => $product->name ?? $product->ten_san_pham ?? 'Sản phẩm',
                'unit_price'   => $price,
                'quantity'     => (int) $validated['quantity'],
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'code'    => 201,
            'message' => 'Đã thêm vào giỏ hàng.',
            'data'    => $cart[$id],
        ], 201);
    }

    public function updateCart(Request $request, int $productId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        if (!isset($cart[$productId])) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'message' => 'Sản phẩm không có trong giỏ hàng.',
                'errors'  => null,
            ], 404);
        }

        $product = Product::findOrFail($productId);
        $cart[$productId]['quantity']   = (int) $validated['quantity'];
        $cart[$productId]['unit_price'] = (float) ($product->price ?? $product->don_gia ?? 0);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Cập nhật giỏ hàng thành công.',
            'data'    => $cart[$productId],
        ]);
    }

    public function removeFromCart(int $productId)
    {
        $cart = session()->get('cart', []);
        if (!isset($cart[$productId])) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'message' => 'Không tìm thấy sản phẩm trong giỏ.',
                'errors'  => null,
            ], 404);
        }

        unset($cart[$productId]);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'data'    => null,
        ]);
    }

    public function updateOrderStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'trang_thai' => 'required|in:ChoDuyet,DaXacNhan,DangGiao,DaGiao,DaHuy',
        ]);

        $order = Order::findOrFail($id);
        $order->trang_thai = $validated['trang_thai'];

        if ($validated['trang_thai'] === 'DaGiao' && empty($order->ngay_giao_thuc_te)) {
            $order->ngay_giao_thuc_te = now();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Cập nhật trạng thái đơn hàng thành công.',
            'data'    => $order,
        ]);
    }
}