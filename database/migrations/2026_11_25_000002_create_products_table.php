<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.3 - products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->string('unit', 30)->default('kg');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('thumbnail_url');
            $table->text('description')->nullable();
            $table->boolean('is_subscription')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_price CHECK (base_price >= 0 AND (discount_price IS NULL OR discount_price >= 0))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
