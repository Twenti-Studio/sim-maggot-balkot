<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\DailyReport;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $initialPassword = env('INITIAL_ADMIN_PASSWORD');
        if (app()->environment('production') && blank($initialPassword)) {
            throw new \RuntimeException('INITIAL_ADMIN_PASSWORD wajib diatur pada environment production.');
        }
        $initialPassword ??= 'password';

        $location = Location::firstOrCreate(['code' => 'RM-UTAMA'], ['name' => 'Rumah Maggot Utama', 'address' => 'Lokasi operasional utama', 'is_active' => true]);

        $accounts = [
            ['name' => 'Super Admin', 'email' => 'superadmin@rumahmaggot.id', 'role' => 'super_admin'],
            ['name' => 'Admin Pengelola', 'email' => 'admin@rumahmaggot.id', 'role' => 'admin'],
            ['name' => 'Petugas Operasional', 'email' => 'petugas@rumahmaggot.id', 'role' => 'operator'],
            ['name' => 'Bendahara', 'email' => 'bendahara@rumahmaggot.id', 'role' => 'treasurer'],
        ];

        $users = [];
        foreach ($accounts as $account) {
            $users[$account['role']] = User::updateOrCreate(['email' => $account['email']], $account + [
                'password' => Hash::make($initialPassword), 'location_id' => $location->id, 'is_active' => true,
            ]);
        }

        $staff = Staff::firstOrCreate(['employee_code' => 'PTG-001'], [
            'user_id' => $users['operator']->id, 'location_id' => $location->id, 'name' => 'Petugas Operasional',
            'position' => 'Petugas Budidaya', 'phone' => '081234567890', 'joined_at' => now()->subYear(), 'status' => 'active',
        ]);

        DailyReport::firstOrCreate(['location_id' => $location->id, 'report_date' => today()->subDay()], [
            'organic_waste_kg' => 125, 'organic_source' => 'Pasar lokal', 'non_organic_waste_kg' => 8,
            'non_organic_type' => 'Plastik campuran', 'eggs_amount' => 250, 'eggs_unit' => 'gram',
            'baby_maggot_kg' => 18, 'adult_maggot_kg' => 42, 'prepupa_kg' => 5, 'bsf_flies_count' => 1250,
            'kasgot_kg' => 35, 'other_fertilizer_kg' => 4, 'wet_maggot_kg' => 28, 'dry_maggot_kg' => 6,
            'status' => 'validated', 'created_by' => $users['operator']->id, 'validated_by' => $users['admin']->id,
            'submitted_at' => now()->subDay(), 'validated_at' => now()->subDay(),
        ]);

        $asset = Asset::firstOrCreate(['asset_code' => 'AST-001'], [
            'location_id' => $location->id, 'name' => 'Mesin Pencacah Organik', 'category' => 'Mesin',
            'location_detail' => 'Area pengolahan', 'purchased_at' => now()->subMonths(8), 'purchase_value' => 7500000,
            'condition' => 'needs_maintenance', 'status' => 'active', 'created_by' => $users['admin']->id,
        ]);
        MaintenanceRecord::firstOrCreate(['asset_id' => $asset->id, 'scheduled_at' => today()->addDays(3)], [
            'assigned_staff_id' => $staff->id, 'maintenance_type' => 'Pemeriksaan pisau dan motor', 'status' => 'scheduled',
            'estimated_cost' => 250000, 'actual_cost' => 0, 'created_by' => $users['admin']->id,
        ]);
    }
}
