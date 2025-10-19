<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiUser;
use App\Models\customers;
use App\Models\WarrantyRecord;
use Illuminate\Support\Facades\Hash;

class WefixSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin API user (no customer link needed)
        ApiUser::create([
            'name' => 'WeFix Admin',
            'email' => 'wefixtvrepair@gmail.com',
            'phone' => '+94773300905',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone_verified' => true,
            'notification_preferences' => [
                'email' => true,
                'push' => true,
                'sms' => true,
            ],
        ]);

        // Create test customer in customers table
        $testCustomer = customers::create([
            'name' => 'Test Customer',
            'phone' => '+94771234567',
            'email' => 'customer@example.com',
            'address' => 'No. 123, Main Street, Colombo 03',
            'pos_code' => 'POS001',
            'store_credit' => 5000.00,
        ]);

        // Create API user linked to customer
        ApiUser::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '+94771234567',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'phone_verified' => true,
            'customer_id' => $testCustomer->id,
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
