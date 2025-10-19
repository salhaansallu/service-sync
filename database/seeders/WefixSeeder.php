<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WarrantyRecord;
use Illuminate\Support\Facades\Hash;

class WefixSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'WeFix Admin',
            'email' => 'wefixtvrepair@gmail.com',
            'password' => Hash::make('admin123'),
            'phone' => '+94773300905',
            'role' => 'admin',
            'phone_verified' => true,
            'notification_preferences' => [
                'email' => true,
                'push' => true,
                'sms' => true,
            ],
        ]);

        // Create test customer
        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '+94771234567',
            'role' => 'customer',
            'phone_verified' => true,
            'notification_preferences' => [
                'email' => true,
                'push' => true,
                'sms' => false,
            ],
        ]);

        // Create sample warranty records
        WarrantyRecord::create([
            'serial_number' => 'SN123456789',
            'bill_number' => 'BILL-2024-001',
            'phone_number' => '+94771234567',
            'product_name' => 'Samsung 55" LED TV',
            'purchase_date' => now()->subMonths(6),
            'expiry_date' => now()->addMonths(6),
            'coverage_type' => 'Full Warranty',
            'notes' => 'Covers parts and labor',
            'is_active' => true,
        ]);

        WarrantyRecord::create([
            'serial_number' => 'SN987654321',
            'bill_number' => 'BILL-2023-050',
            'phone_number' => '+94771234567',
            'product_name' => 'LG 43" Smart TV',
            'purchase_date' => now()->subMonths(18),
            'expiry_date' => now()->subMonths(6),
            'coverage_type' => 'Full Warranty',
            'notes' => 'Warranty expired',
            'is_active' => true,
        ]);
    }
}
