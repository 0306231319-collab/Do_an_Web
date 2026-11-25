<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Mật khẩu chung cho tài khoản mẫu: password (tự băm Bcrypt nhờ cast 'hashed')
        $users = [
            ['full_name' => 'Quản trị viên', 'email' => 'admin@nongsan.test', 'phone' => '0900000001', 'role' => User::ROLE_ADMIN],
            ['full_name' => 'Nhân viên nông trại', 'email' => 'staff@nongsan.test', 'phone' => '0900000002', 'role' => User::ROLE_FARM_STAFF],
            ['full_name' => 'Nhân viên bán hàng', 'email' => 'sales@nongsan.test', 'phone' => '0900000003', 'role' => User::ROLE_SALES],
            ['full_name' => 'Khách hàng Một', 'email' => 'khach1@nongsan.test', 'phone' => '0900000004', 'role' => User::ROLE_CUSTOMER],
            ['full_name' => 'Khách hàng Hai', 'email' => 'khach2@nongsan.test', 'phone' => '0900000005', 'role' => User::ROLE_CUSTOMER],
        ];

        foreach ($users as $user) {
            User::create($user + ['password' => 'password', 'is_active' => true]);
        }

        $admin = User::where('role', User::ROLE_ADMIN)->first();

        SystemConfig::create([
            'updated_by' => $admin->id,
            'config_key' => 'near_expiry_alert_days',
            'config_value' => '2',
            'description' => 'Số ngày trước hạn sử dụng để cảnh báo xả hàng (UC5, mặc định 2 ngày).',
        ]);
    }
}
