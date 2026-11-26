<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Chạy: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CatalogSeeder::class,
            BatchSeeder::class,
            OrderSeeder::class,
            SubscriptionSeeder::class,
        ]);

        DB::table('system_configs')->updateOrInsert(
            ['key' => 'expiry_warning_days'],
            ['value' => '2']
        );
    }
}