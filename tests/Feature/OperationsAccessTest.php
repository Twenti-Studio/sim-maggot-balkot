<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\DailyReport;
use App\Models\Location;
use App\Models\User;
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

        foreach ([
            route('dashboard'), route('reports.index'), route('reports.create'), route('reports.show', $report),
            route('staff.index'), route('attendance.index'), route('assets.index'), route('assets.show', $asset),
            route('users.index'), route('rbac.index'), route('locations.index'), route('notifications.index'), route('exports.index'),
        ] as $url) {
            $this->actingAs($superAdmin)->get($url)->assertOk();
        }
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
}
