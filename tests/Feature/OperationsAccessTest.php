<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\DailyReport;
use App\Models\FinancialTransaction;
use App\Models\Location;
use App\Models\Staff;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsAccessTest extends TestCase
{
    use RefreshDatabase;

    private Location $location;

    protected function setUp(): void
    {
        parent::setUp();
        $this->location = Location::create(['code' => 'TEST', 'name' => 'Lokasi Test', 'is_active' => true]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_super_admin_can_manage_users_and_operator_cannot(): void
    {
        $superAdmin = $this->user('super_admin');
        $operator = $this->user('operator');

        $this->actingAs($superAdmin)->get(route('users.index'))->assertOk();
        $this->actingAs($operator)->get(route('users.index'))->assertForbidden();
    }

    public function test_super_admin_and_admin_can_create_staff_and_treasurer_accounts(): void
    {
        $superAdmin = $this->user('super_admin');
        $admin = $this->user('admin');

        $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Petugas Baru',
            'email' => 'petugas-baru@example.test',
            'password' => 'password-baru',
            'role' => 'operator',
            'phone' => '081234567890',
            'location_id' => $this->location->id,
            'is_active' => '1',
        ])->assertRedirect();

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Bendahara Baru',
            'email' => 'bendahara-baru@example.test',
            'password' => 'password-baru',
            'role' => 'treasurer',
            'phone' => '081234567891',
            'location_id' => $this->location->id,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'petugas-baru@example.test', 'role' => 'operator', 'is_active' => true]);
        $this->assertDatabaseHas('users', ['email' => 'bendahara-baru@example.test', 'role' => 'treasurer', 'is_active' => true]);
    }

    public function test_admin_cannot_create_or_deactivate_super_admin_account(): void
    {
        $admin = $this->user('admin');
        $superAdmin = $this->user('super_admin');

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Super Admin Baru',
            'email' => 'superadmin-baru@example.test',
            'password' => 'password-baru',
            'role' => 'super_admin',
            'location_id' => $this->location->id,
            'is_active' => '1',
        ])->assertForbidden();

        $this->actingAs($admin)->delete(route('users.destroy', $superAdmin))->assertForbidden();
    }

    public function test_treasurer_cannot_access_operational_reports(): void
    {
        $this->actingAs($this->user('treasurer'))->get(route('reports.index'))->assertForbidden();
    }

    public function test_operator_can_edit_own_draft_but_not_validated_report(): void
    {
        $operator = $this->user('operator');
        $draft = $this->report($operator, 'draft');
        $validated = $this->report($operator, 'validated', today()->subDay());

        $this->actingAs($operator)->get(route('reports.edit', $draft))->assertOk();
        $this->actingAs($operator)->get(route('reports.edit', $validated))->assertForbidden();
    }

    public function test_admin_can_validate_submitted_report(): void
    {
        $operator = $this->user('operator');
        $admin = $this->user('admin');
        $report = $this->report($operator, 'submitted');

        $this->actingAs($admin)->post(route('reports.validate', $report))->assertRedirect();
        $this->assertDatabaseHas('daily_reports', ['id' => $report->id, 'status' => 'validated', 'validated_by' => $admin->id]);
    }

    public function test_operator_can_view_assets_but_cannot_create_them(): void
    {
        $operator = $this->user('operator');
        Asset::create(['location_id' => $this->location->id, 'asset_code' => 'A-1', 'name' => 'Aset', 'category' => 'Alat', 'created_by' => $operator->id]);

        $this->actingAs($operator)->get(route('assets.index'))->assertOk();
        $this->actingAs($operator)->get(route('assets.create'))->assertForbidden();
    }

    public function test_pwa_files_are_publicly_available(): void
    {
        $this->assertFileExists(public_path('manifest.webmanifest'));
        $this->assertFileExists(public_path('service-worker.js'));
        $this->assertFileExists(public_path('offline.html'));
    }

    public function test_super_admin_operational_pages_render(): void
    {
        $superAdmin = $this->user('super_admin');
        $report = $this->report($superAdmin, 'draft');
        $asset = Asset::create(['location_id' => $this->location->id, 'asset_code' => 'A-2', 'name' => 'Biopond', 'category' => 'Budidaya', 'created_by' => $superAdmin->id]);
        $this->staff($superAdmin);

        foreach ([
            route('dashboard'), route('reports.index'), route('reports.create'), route('reports.show', $report),
            route('staff.index'), route('attendance.index'), route('assets.index'), route('assets.show', $asset),
            route('payroll.index'), route('payroll.create'), route('finance.index'), route('finance.create'),
            route('waste-types.index'), route('waste-deposits.index'), route('waste-deposits.create'),
            route('users.index'), route('locations.index'), route('notifications.index'), route('exports.index'),
        ] as $url) {
            $this->actingAs($superAdmin)->get($url)->assertOk();
        }
    }

    public function test_public_registration_creates_regular_user_with_required_location(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Nasabah Sampah',
            'email' => 'nasabah@example.test',
            'phone' => '081234567899',
            'location_id' => $this->location->id,
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect(route('savings.index'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'nasabah@example.test',
            'role' => 'user',
            'location_id' => $this->location->id,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_manages_waste_types_and_operator_records_deposit(): void
    {
        $superAdmin = $this->user('super_admin');
        $operator = $this->user('operator');
        $customer = $this->user('user');
        $this->staff($operator);

        $this->actingAs($superAdmin)->post(route('waste-types.store'), [
            'name' => 'Botol plastik',
            'unit' => 'kg',
            'price_per_unit' => 2500,
            'description' => 'Bersih dan terpilah',
            'is_active' => '1',
        ])->assertRedirect();
        $this->actingAs($superAdmin)->post(route('waste-types.store'), [
            'name' => 'Kardus',
            'unit' => 'kg',
            'price_per_unit' => 1800,
            'description' => 'Kering',
            'is_active' => '1',
        ])->assertRedirect();

        $plastic = WasteType::where('name', 'Botol plastik')->firstOrFail();
        $cardboard = WasteType::where('name', 'Kardus')->firstOrFail();
        $this->actingAs($operator)->post(route('waste-deposits.store'), [
            'user_id' => $customer->id,
            'deposit_date' => today()->format('Y-m-d'),
            'items' => [
                $plastic->id => ['selected' => '1', 'weight' => '3.5'],
                $cardboard->id => ['selected' => '1', 'weight' => '0.75'],
            ],
        ])->assertRedirect(route('waste-deposits.index'));

        $this->assertDatabaseHas('waste_deposits', [
            'user_id' => $customer->id,
            'waste_type_id' => $plastic->id,
            'total_amount' => 8750,
            'created_by' => $operator->id,
        ]);
        $this->assertDatabaseHas('waste_deposits', [
            'user_id' => $customer->id,
            'waste_type_id' => $cardboard->id,
            'total_amount' => 1350,
            'created_by' => $operator->id,
        ]);
        $this->actingAs($customer)->get(route('savings.history'))->assertOk()->assertSee('Rp8.750')->assertSee('Rp1.350');
    }

    public function test_application_timezone_uses_wita(): void
    {
        $this->assertSame('Asia/Makassar', config('app.timezone'));
        $this->assertSame('Asia/Makassar', now()->timezoneName);
    }

    public function test_treasurer_can_create_finance_but_cannot_validate_it(): void
    {
        $treasurer = $this->user('treasurer');
        $admin = $this->user('admin');

        $this->actingAs($treasurer)->post(route('finance.store'), [
            'location_id' => $this->location->id,
            'transaction_date' => today()->format('Y-m-d'),
            'type' => 'cash_in',
            'category' => 'Penjualan',
            'description' => 'Penjualan maggot basah',
            'amount' => 125000,
            'payment_method' => 'Tunai',
            'status' => 'validated',
        ])->assertRedirect();

        $transaction = FinancialTransaction::firstOrFail();
        $this->assertSame('draft', $transaction->status);
        $this->actingAs($treasurer)->post(route('finance.validate', $transaction))->assertForbidden();
        $this->actingAs($admin)->post(route('finance.validate', $transaction))->assertRedirect();
        $this->assertDatabaseHas('financial_transactions', ['id' => $transaction->id, 'status' => 'validated', 'validated_by' => $admin->id]);
    }

    public function test_admin_can_export_operational_reports_as_pdf_and_excel(): void
    {
        $admin = $this->user('admin');
        $this->report($admin, 'validated');

        $this->actingAs($admin)->get(route('exports.download', ['production', 'pdf']))
            ->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->actingAs($admin)->get(route('exports.download', ['production', 'xlsx']))
            ->assertOk()->assertDownload();
    }

    public function test_treasurer_cannot_export_operational_reports(): void
    {
        $this->actingAs($this->user('treasurer'))->get(route('exports.index'))->assertForbidden();
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'location_id' => $this->location->id, 'is_active' => true]);
    }

    private function report(User $creator, string $status, $date = null): DailyReport
    {
        return DailyReport::create([
            'location_id' => $this->location->id,
            'report_date' => $date ?: today(),
            'status' => $status,
            'created_by' => $creator->id,
        ]);
    }

    private function staff(User $user): Staff
    {
        return Staff::create([
            'user_id' => $user->id,
            'location_id' => $this->location->id,
            'employee_code' => 'PTG-'.$user->id,
            'name' => $user->name,
            'position' => 'Petugas',
            'status' => 'active',
        ]);
    }
}
