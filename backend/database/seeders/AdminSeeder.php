<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\AdminSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'pavu2701@gmail.com',
            'password_hash' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        AdminSession::create([
            'admin_id' => $admin->id,
            'refresh_token' => hash('sha256', Str::random(40)),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Seeder',
            'is_trusted' => true,
            'expires_at' => now()->addDays(7),
        ]);

        $admin->activityLogs()->create([
            'severity' => 'low',
            'action_type' => 'auth',
            'action' => 'Admin account created by seeder',
        ]);
    }
}
