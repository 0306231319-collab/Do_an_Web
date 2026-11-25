<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'rau-an-la' => Category::create(['name' => 'Rau ăn lá', 'slug' => 'rau-an-la', 'description' => 'Các loại rau ăn lá tươi.']),
            'cu-qua' => Category::create(['name' => 'Củ quả', 'slug' => 'cu-qua', 'description' => 'Củ và quả dùng nấu ăn.']),
            'trai-cay' => Category::create(['name' => 'Trái cây', 'slug' => 'trai-cay', 'description' => 'Trái cây theo mùa.']),
        ];

        // [sku, tên, slug, danh mục, đơn vị, giá, cho phép gói định kỳ]
        $products = [
            ['TC-BEEF-01', 'Cà chua Beef hữu cơ Đà Lạt', 'ca-chua-beef-huu-co-da-lat', 'cu-qua', 'kg', 45000, true],
            ['TC-BI-01', 'Cà chua bi', 'ca-chua-bi', 'cu-qua', 'kg', 60000, true],
            ['RM-01', 'Rau muống', 'rau-muong', 'rau-an-la', 'bó', 15000, true],
            ['XL-01', 'Xà lách thủy canh', 'xa-lach-thuy-canh', 'rau-an-la', 'kg', 40000, true],
            ['CR-01', 'Cà rốt Đà Lạt', 'ca-rot-da-lat', 'cu-qua', 'kg', 30000, true],
            ['DT-01', 'Dâu tây Đà Lạt', 'dau-tay-da-lat', 'trai-cay', 'hộp', 120000, false],
        ];

        foreach ($products as [$sku, $name, $slug, $cat, $unit, $price, $sub]) {
            Product::create([
                'category_id' => $categories[$cat]->id,
                'sku' => $sku,
                'name' => $name,
                'slug' => $slug,
                'unit' => $unit,
                'base_price' => $price,
                'thumbnail_url' => '/images/products/' . $slug . '.jpg',
                'description' => 'Nông sản canh tác tại nông trại (dữ liệu mẫu).',
                'is_subscription' => $sub,
                'status' => 'active',
            ]);
        }
    }
}
