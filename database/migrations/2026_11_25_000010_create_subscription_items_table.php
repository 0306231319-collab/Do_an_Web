<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.12 - subscription_items
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('so_luong', 10, 2);
            $table->dateTime('ngay_tao')->useCurrent();
            $table->dateTime('ngay_cap_nhat')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['subscription_id', 'product_id'], 'uk_subscription_items_sub_product');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE subscription_items ADD CONSTRAINT chk_subscription_items_so_luong CHECK (so_luong > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};
