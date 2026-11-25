<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng 3.7 - system_configs (ví dụ near_expiry_alert_days = 2)
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->string('config_key', 100)->unique();
            $table->string('config_value');
            $table->text('description')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
