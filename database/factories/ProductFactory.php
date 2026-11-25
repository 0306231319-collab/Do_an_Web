<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Dùng sau khi đã có danh mục (chạy CatalogSeeder trước).
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Nông sản ' . fake()->unique()->numberBetween(100, 999);

        return [
            'category_id' => Category::query()->inRandomOrder()->value('id'),
            'sku' => 'SP-' . fake()->unique()->numerify('#####'),
            'name' => $name,
            'slug' => Str::slug($name),
            'unit' => 'kg',
            'base_price' => fake()->numberBetween(10, 150) * 1000,
            'discount_price' => null,
            'thumbnail_url' => '/images/products/placeholder.jpg',
            'description' => 'Dữ liệu mẫu sinh tự động.',
            'is_subscription' => true,
            'status' => 'active',
        ];
    }
}
