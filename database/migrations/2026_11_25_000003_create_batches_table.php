<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.4 - batches (cốt lõi FEFO và truy xuất QR)
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('batch_code', 50)->unique();
            $table->string('qr_code_url')->unique();
            $table->date('harvest_date');
            $table->date('expiry_date');
            $table->decimal('initial_quantity', 10, 2);
            $table->decimal('current_quantity', 10, 2);
            $table->enum('status', ['available', 'empty', 'near_expiry', 'expired'])->default('available');
            $table->timestamps();

            // Khóa tham chiếu cho khóa ngoại ghép (batch_id, product_id) từ order_details
            $table->unique(['id', 'product_id'], 'uk_batches_id_product');
            // Chỉ mục phục vụ thuật toán FEFO
            $table->index(['product_id', 'expiry_date', 'current_quantity'], 'idx_batches_fefo');
            $table->index('expiry_date', 'idx_batches_expiry_date');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE batches ADD CONSTRAINT chk_batches_quantity CHECK (initial_quantity > 0 AND current_quantity >= 0 AND current_quantity <= initial_quantity)');
            DB::statement('ALTER TABLE batches ADD CONSTRAINT chk_batches_dates CHECK (expiry_date >= harvest_date)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
