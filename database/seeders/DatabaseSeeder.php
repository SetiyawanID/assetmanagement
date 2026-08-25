<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create(['name' => 'Super Admin', 'email' => 'admin@assethub.test', 'role' => 'super_admin', 'password' => 'password']);
        $user = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@assethub.test', 'role' => 'user', 'password' => 'password']);
        $laptop = Category::create(['name' => 'Laptop', 'description' => 'Notebook dan perangkat kerja mobile']);
        $network = Category::create(['name' => 'Network', 'description' => 'Perangkat jaringan dan konektivitas']);
        $office = Location::create(['name' => 'Ruang IT', 'floor' => 'Lantai 1']);
        $meeting = Location::create(['name' => 'Ruang Meeting', 'floor' => 'Lantai 2']);
        Asset::create(['asset_tag' => 'AST-0001', 'name' => 'MacBook Pro 14', 'category_id' => $laptop->id, 'location_id' => $office->id, 'assigned_to' => $user->id, 'brand' => 'Apple', 'model' => 'M3 Pro', 'serial_number' => 'C02IT001', 'purchase_date' => '2025-01-20', 'purchase_price' => 32000000, 'status' => 'assigned', 'condition' => 'excellent', 'warranty_until' => '2028-01-20']);
        Asset::create(['asset_tag' => 'AST-0002', 'name' => 'Dell Latitude 5440', 'category_id' => $laptop->id, 'location_id' => $office->id, 'brand' => 'Dell', 'model' => 'Latitude 5440', 'serial_number' => 'DL544002', 'purchase_date' => '2024-08-10', 'purchase_price' => 14500000, 'status' => 'available', 'condition' => 'good']);
        Asset::create(['asset_tag' => 'AST-0003', 'name' => 'Managed Switch 24 Port', 'category_id' => $network->id, 'location_id' => $meeting->id, 'brand' => 'TP-Link', 'model' => 'T1600G', 'serial_number' => 'TPL003', 'purchase_date' => '2023-05-12', 'purchase_price' => 2800000, 'status' => 'maintenance', 'condition' => 'fair']);
    }
}
