<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.9 - order_details (liên kết lô FEFO)
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            // NULL khi đơn đang ChoDuyet; bắt buộc có trước khi đơn chuyển sang DaXacNhan (kiểm tra ở FEFO Service)
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->decimal('so_luong', 10, 2);
            $table->decimal('don_gia', 12, 2);
            $table->decimal('thanh_tien', 12, 2);

            // Khóa ngoại ghép: lô phải thuộc đúng sản phẩm của dòng đơn (nhận xét Tuần 3 của thầy)
            $table->foreign(['batch_id', 'product_id'], 'fk_order_details_batch_product')
                ->references(['id', 'product_id'])->on('batches')
                ->restrictOnDelete();
            $table->index(['order_id', 'batch_id'], 'idx_order_details_order_batch');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE order_details ADD CONSTRAINT chk_order_details_values CHECK (so_luong > 0 AND don_gia >= 0 AND thanh_tien >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
