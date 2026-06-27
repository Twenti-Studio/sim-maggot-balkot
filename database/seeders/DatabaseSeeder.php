<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\DailyReport;
use App\Models\FinancialTransaction;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\PayrollRecord;
use App\Models\Staff;
use App\Models\User;
use App\Models\WasteDeposit;
use App\Models\WasteType;
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

        $location = Location::firstOrCreate(['code' => 'RM-UTAMA'], ['name' => 'Maggot Balkot Utama', 'address' => 'Lokasi operasional utama', 'is_active' => true]);

        $accounts = [
            ['name' => 'Super Admin', 'email' => 'superadmin@rumahmaggot.id', 'role' => 'super_admin'],
            ['name' => 'Admin Pengelola', 'email' => 'admin@rumahmaggot.id', 'role' => 'admin'],
            ['name' => 'Petugas Operasional', 'email' => 'petugas@rumahmaggot.id', 'role' => 'operator'],
            ['name' => 'Bendahara', 'email' => 'bendahara@rumahmaggot.id', 'role' => 'treasurer'],
            ['name' => 'User Sampah', 'email' => 'user@rumahmaggot.id', 'role' => 'user'],
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

        $plastic = WasteType::firstOrCreate(['name' => 'Botol plastik'], [
            'category' => 'Daur ulang', 'unit' => 'kg', 'price_per_unit' => 2500, 'description' => 'Botol plastik bersih dan sudah dipilah.',
            'is_active' => true, 'created_by' => $users['super_admin']->id,
        ]);
        WasteType::firstOrCreate(['name' => 'Kardus'], [
            'category' => 'Daur ulang', 'unit' => 'kg', 'price_per_unit' => 1800, 'description' => 'Kardus kering tanpa sisa makanan.',
            'is_active' => true, 'created_by' => $users['super_admin']->id,
        ]);
        WasteDeposit::firstOrCreate(['user_id' => $users['user']->id, 'waste_type_id' => $plastic->id, 'deposit_date' => today()], [
            'location_id' => $location->id, 'weight' => 4.5, 'price_per_unit' => $plastic->price_per_unit,
            'total_amount' => 4.5 * (float) $plastic->price_per_unit, 'notes' => 'Contoh setoran awal.',
            'created_by' => $users['operator']->id,
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

        PayrollRecord::firstOrCreate(['staff_id' => $staff->id, 'period_start' => now()->startOfMonth()->toDateString()], [
            'period_end' => now()->endOfMonth()->toDateString(), 'base_salary' => 2500000, 'bonus' => 350000,
            'deductions' => 0, 'total_paid' => 2850000, 'paid_at' => today(), 'status' => 'paid',
            'notes' => 'Contoh pembayaran gaji dan bonus bulanan.', 'created_by' => $users['treasurer']->id,
        ]);

        FinancialTransaction::firstOrCreate(['reference_no' => 'SALE-'.today()->format('Ym').'-001'], [
            'location_id' => $location->id, 'transaction_date' => today(), 'type' => 'cashier_sale',
            'category' => 'Penjualan', 'description' => 'Penjualan maggot basah', 'amount' => 950000,
            'payment_method' => 'Tunai', 'counterparty' => 'Pelanggan lokal', 'status' => 'validated',
            'created_by' => $users['treasurer']->id, 'validated_by' => $users['admin']->id, 'validated_at' => now(),
        ]);
        FinancialTransaction::firstOrCreate(['reference_no' => 'OUT-'.today()->format('Ym').'-001'], [
            'location_id' => $location->id, 'transaction_date' => today(), 'type' => 'cash_out',
            'category' => 'Operasional', 'description' => 'Pembelian bahan pendukung budidaya', 'amount' => 275000,
            'payment_method' => 'Transfer', 'counterparty' => 'Pemasok bahan', 'status' => 'validated',
            'created_by' => $users['treasurer']->id, 'validated_by' => $users['admin']->id, 'validated_at' => now(),
        ]);
    }
}
