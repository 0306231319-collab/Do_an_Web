<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\SubscriptionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->withCount('batches')->orderByDesc('id');

        $keyword = trim((string) $request->input('q'));
        if ($keyword !== '') {
            $query->where(function ($w) use ($keyword) {
                $w->where('name', 'like', "%{$keyword}%")->orWhere('sku', 'like', "%{$keyword}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'keyword'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', ['product' => new Product(['unit' => 'kg', 'status' => 'active', 'is_subscription' => true]), 'categories' => $categories]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? '') !== '' ? $data['slug'] : $data['name']);
        $data['is_subscription'] = $request->boolean('is_subscription');
        $data['thumbnail_url'] = $request->hasFile('thumbnail')
            ? Storage::url($request->file('thumbnail')->store('products', 'public'))
            : '/images/products/placeholder.jpg';
        unset($data['thumbnail']);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') !== '' ? $data['slug'] : $this->uniqueSlug($data['name'], $product->id);
        $data['is_subscription'] = $request->boolean('is_subscription');

        if ($request->hasFile('thumbnail')) {
            $this->deleteStoredImage($product->thumbnail_url);
            $data['thumbnail_url'] = Storage::url($request->file('thumbnail')->store('products', 'public'));
        }
        unset($data['thumbnail']);

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(Product $product)
    {
        $inUse = $product->batches()->exists()
            || OrderDetail::where('product_id', $product->id)->exists()
            || SubscriptionItem::where('product_id', $product->id)->exists();

        if ($inUse) {
            return back()->with('error', 'Sản phẩm đã có lô hàng hoặc đơn hàng nên không xóa được. Hãy chuyển sang trạng thái Ngừng bán.');
        }

        $this->deleteStoredImage($product->thumbnail_url);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'san-pham';
        $slug = $base;
        $i = 2;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function deleteStoredImage(?string $url): void
    {
        if ($url && Str::startsWith($url, '/storage/')) {
            Storage::disk('public')->delete(Str::after($url, '/storage/'));
        }
    }
}
