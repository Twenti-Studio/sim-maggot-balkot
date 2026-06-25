<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\MaintenanceRecord;
use App\Models\Staff;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function create(Asset $asset, Request $request)
    {
        return view('maintenance.form', ['asset' => $asset, 'maintenance' => new MaintenanceRecord, 'staffMembers' => Staff::where('status', 'active')->get()]);
    }

    public function store(Request $request, Asset $asset)
    {
        $data = $this->validated($request);
        if ($request->user()->role === 'operator') {
            abort_unless($request->user()->staff, 403);
            $data['assigned_staff_id'] = $request->user()->staff->id;
        }
        $maintenance = $asset->maintenances()->create($data + ['created_by' => $request->user()->id]);
        AuditLog::record('maintenance.created', $maintenance);
        $assignee = $maintenance->assignedStaff?->user;
        if ($assignee && $assignee->id !== $request->user()->id) {
            $this->notifications->notify([$assignee], 'maintenance_assigned', 'Tugas perawatan baru', "Perawatan {$asset->name} dijadwalkan.", route('assets.show', $asset));
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Jadwal perawatan ditambahkan.');
    }

    public function edit(Asset $asset, MaintenanceRecord $maintenance, Request $request)
    {
        abort_unless($maintenance->asset_id === $asset->id, 404);
        if ($request->user()->role === 'operator') {
            abort_unless($maintenance->assigned_staff_id === $request->user()->staff?->id, 403);
        }

        return view('maintenance.form', ['asset' => $asset, 'maintenance' => $maintenance, 'staffMembers' => Staff::where('status', 'active')->get()]);
    }

    public function update(Request $request, Asset $asset, MaintenanceRecord $maintenance)
    {
        abort_unless($maintenance->asset_id === $asset->id, 404);
        if ($request->user()->role === 'operator') {
            abort_unless($maintenance->assigned_staff_id === $request->user()->staff?->id, 403);
        }
        $old = $maintenance->toArray();
        $data = $this->validated($request);
        if ($data['status'] === 'completed' && empty($data['completed_at'])) {
            $data['completed_at'] = today();
        }
        $maintenance->update($data);
        AuditLog::record('maintenance.updated', $maintenance, $old, $maintenance->fresh()->toArray());

        return redirect()->route('assets.show', $asset)->with('success', 'Perawatan diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'assigned_staff_id' => ['nullable', 'exists:staff,id'], 'scheduled_at' => ['required', 'date'], 'completed_at' => ['nullable', 'date'],
            'maintenance_type' => ['required', 'string', 'max:150'], 'status' => ['required', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
            'estimated_cost' => ['required', 'numeric', 'min:0'], 'actual_cost' => ['required', 'numeric', 'min:0'],
            'actions' => ['nullable', 'string'], 'components' => ['nullable', 'string'], 'notes' => ['nullable', 'string'],
        ]);
    }
}
