<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CartOrderApiController extends Controller
{
    private function sellingPrice(Product $product): float
    {
        $discount = $product->discount_price;

        return (float) (($discount !== null && (float) $discount > 0)
            ? $discount
            : $product->base_price);
    }

    private function cartPayload(array $cart): array
    {
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $cartItem) {
            $product = Product::query()
                ->whereKey($productId)
                ->where('status', 'active')
                ->first();

            if (!$product) {
                continue;
            }

            $quantity = (int) ($cartItem['quantity'] ?? 1);
            $price = $this->sellingPrice($product);
            $subtotal = round($price * $quantity, 2);

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'thumbnail_url' => $product->thumbnail_url,
                'unit' => $product->unit,
                'unit_price' => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }

        return ['items' => $items, 'total_amount' => round($total, 2)];
    }

    public function getCart(Request $request)
    {
        $cart = session()->get('cart', []);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Lấy giỏ hàng thành công.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()
            ->whereKey($validated['product_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $cart = session()->get('cart', []);
        $id = (int) $product->id;
        $cart[$id] = [
            'product_id' => $id,
            'quantity' => min(99, (int) ($cart[$id]['quantity'] ?? 0) + (int) $validated['quantity']),
        ];
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'code' => 201,
            'message' => 'Đã thêm vào giỏ hàng.',
            'data' => $this->cartPayload($cart),
        ], 201);
    }

    public function updateCart(Request $request, int $productId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session()->get('cart', []);
        if (!isset($cart[$productId])) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Sản phẩm không có trong giỏ hàng.',
                'errors' => null,
            ], 404);
        }

        $product = Product::query()
            ->whereKey($productId)
            ->where('status', 'active')
            ->first();

        if (!$product) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Sản phẩm không còn được kinh doanh.',
                'errors' => null,
            ], 404);
        }

        $cart[$productId]['quantity'] = (int) $validated['quantity'];
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Cập nhật giỏ hàng thành công.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function removeFromCart(int $productId)
    {
        $cart = session()->get('cart', []);
        if (!isset($cart[$productId])) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Không tìm thấy sản phẩm trong giỏ.',
                'errors' => null,
            ], 404);
        }

        unset($cart[$productId]);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function updateOrderStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'trang_thai' => 'required|in:ChoDuyet,DaXacNhan,DangGiao,DaGiao,DaHuy',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['trang_thai' => $validated['trang_thai']]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Cập nhật trạng thái đơn hàng thành công.',
            'data' => $order,
        ]);
    }
}
