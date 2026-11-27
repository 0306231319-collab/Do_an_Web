<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Danh sách sản phẩm có tìm kiếm, lọc theo danh mục & khoảng giá.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // 1. Tìm kiếm theo từ khóa (hỗ trợ cả ?keyword=... và ?search=...)
        $keyword = trim((string) ($request->input('keyword') ?? $request->input('search', '')));
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 2. Lọc theo danh mục (hỗ trợ cả ?category_id=... và ?category=...)
        $categoryId = $request->input('category_id') ?? $request->input('category');
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // 3. Lọc theo khoảng giá (ưu tiên discount_price nếu có, ngược lại dùng base_price)
        if ($request->filled('min_price')) {
            $query->whereRaw('COALESCE(discount_price, base_price) >= ?', [(float) $request->input('min_price')]);
        }

        if ($request->filled('max_price')) {
            $query->whereRaw('COALESCE(discount_price, base_price) <= ?', [(float) $request->input('max_price')]);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('pages.products_index', compact('products', 'categories'));
    }

    /**
     * Chi tiết sản phẩm: kèm lô đang bán FEFO, ngày thu hoạch, hạn sử dụng, QR, timeline.
     */
    public function show(Product $product)
    {
        $activeBatches = $product->batches()
            ->where('current_quantity', '>', 0)
            ->whereDate('expiry_date', '>', now()->toDateString())
            ->orderBy('expiry_date', 'asc')
            ->with(['farmingLogs' => function ($q) {
                $q->with('staff')->orderBy('thoi_gian', 'desc');
            }])
            ->get();

        $currentBatch = $activeBatches->first();

        return view('pages.product_detail', compact('product', 'activeBatches', 'currentBatch'));
    }

    /**
     * Trang Giới thiệu nông trại.
     */
    public function aboutFarm()
    {
        return view('pages.about_farm');
    }
}