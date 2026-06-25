<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DailyReport;
use App\Models\Location;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DailyReportController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request)
    {
        $query = DailyReport::with(['location', 'creator'])->latest('report_date');
        if ($request->user()->role === 'operator') {
            $query->where('created_by', $request->user()->id);
        } elseif (! $request->user()->isSuperAdmin() && $request->user()->location_id) {
            $query->where('location_id', $request->user()->location_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date('date_to'));
        }

        return view('reports.index', ['reports' => $query->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('reports.form', ['report' => new DailyReport, 'locations' => Location::where('is_active', true)->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['location_id'] = $request->user()->isSuperAdmin() || $request->user()->role === 'admin'
            ? $data['location_id'] : $request->user()->location_id;
        $report = DailyReport::create($data);
        AuditLog::record('production.created', $report, [], $report->toArray());

        return redirect()->route('reports.show', $report)->with('success', 'Laporan harian disimpan sebagai draft.');
    }

    public function show(DailyReport $report, Request $request)
    {
        $this->ensureVisible($request, $report);

        return view('reports.show', compact('report'));
    }

    public function edit(DailyReport $report, Request $request)
    {
        abort_unless($report->isEditableBy($request->user()), 403);

        return view('reports.form', ['report' => $report, 'locations' => Location::where('is_active', true)->get()]);
    }

    public function update(Request $request, DailyReport $report)
    {
        abort_unless($report->isEditableBy($request->user()), 403);
        $old = $report->toArray();
        $data = $this->validated($request);
        if ($request->user()->role === 'operator') {
            $data['location_id'] = $request->user()->location_id;
        }
        $report->update($data);
        AuditLog::record('production.updated', $report, $old, $report->fresh()->toArray());

        return redirect()->route('reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function submit(DailyReport $report, Request $request)
    {
        abort_unless($report->created_by === $request->user()->id || $request->user()->hasPermission('production.validate'), 403);
        abort_unless(in_array($report->status, ['draft', 'rejected'], true), 422);
        $report->update(['status' => 'submitted', 'submitted_at' => now(), 'rejection_reason' => null]);
        AuditLog::record('production.submitted', $report);
        $validators = User::whereIn('role', ['super_admin', 'admin'])->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('location_id')->orWhere('location_id', $report->location_id))->get();
        $this->notifications->notify($validators, 'report_submitted', 'Laporan menunggu validasi', "Laporan {$report->report_date->format('d/m/Y')} telah diajukan.", route('reports.show', $report));

        return back()->with('success', 'Laporan diajukan untuk validasi.');
    }

    public function validateReport(DailyReport $report, Request $request)
    {
        abort_unless($request->user()->hasPermission('production.validate') && $report->status === 'submitted', 403);
        $report->update(['status' => 'validated', 'validated_by' => $request->user()->id, 'validated_at' => now(), 'rejection_reason' => null]);
        AuditLog::record('production.validated', $report);
        $this->notifications->notify([$report->creator], 'report_validated', 'Laporan divalidasi', 'Laporan harian Anda telah divalidasi.', route('reports.show', $report));

        return back()->with('success', 'Laporan telah divalidasi.');
    }

    public function reject(Request $request, DailyReport $report)
    {
        abort_unless($request->user()->hasPermission('production.validate') && $report->status === 'submitted', 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $report->update(['status' => 'rejected', 'rejection_reason' => $data['reason'], 'validated_by' => $request->user()->id]);
        AuditLog::record('production.rejected', $report, [], ['reason' => $data['reason']]);
        $this->notifications->notify([$report->creator], 'report_rejected', 'Laporan perlu diperbaiki', $data['reason'], route('reports.show', $report));

        return back()->with('success', 'Laporan ditolak dan dikembalikan kepada Petugas.');
    }

    public function requestRevision(Request $request, DailyReport $report)
    {
        abort_unless($report->created_by === $request->user()->id && $report->status === 'validated', 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $report->update(['status' => 'revision_requested', 'revision_reason' => $data['reason']]);
        $validators = User::whereIn('role', ['super_admin', 'admin'])->where('is_active', true)->get();
        $this->notifications->notify($validators, 'revision_requested', 'Permintaan revisi laporan', $data['reason'], route('reports.show', $report));
        AuditLog::record('production.revision_requested', $report);

        return back()->with('success', 'Permintaan revisi telah diajukan.');
    }

    public function reopen(DailyReport $report, Request $request)
    {
        abort_unless($request->user()->hasPermission('production.validate') && $report->status === 'revision_requested', 403);
        $report->update(['status' => 'rejected', 'validated_at' => null, 'revision_reason' => null, 'rejection_reason' => 'Revisi dibuka oleh pengelola.']);
        $this->notifications->notify([$report->creator], 'revision_opened', 'Revisi laporan dibuka', 'Silakan perbaiki dan ajukan ulang laporan.', route('reports.edit', $report));
        AuditLog::record('production.reopened', $report);

        return back()->with('success', 'Laporan dibuka kembali untuk revisi.');
    }

    public function destroy(DailyReport $report, Request $request)
    {
        abort_unless($request->user()->hasPermission('production.delete'), 403);
        $report->delete();
        AuditLog::record('production.archived', $report);

        return redirect()->route('reports.index')->with('success', 'Laporan diarsipkan.');
    }

    public function export(Request $request)
    {
        abort_unless($request->user()->hasPermission('report.export') && $request->user()->role !== 'treasurer', 403);
        $rows = DailyReport::with(['location', 'creator'])->orderBy('report_date')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Lokasi', 'Petugas', 'Status', 'Sampah Organik (kg)', 'Non-organik (kg)', 'Maggot Basah (kg)', 'Maggot Kering (kg)', 'Kasgot (kg)']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->report_date->format('Y-m-d'), $row->location->name, $row->creator->name, $row->status, $row->organic_waste_kg, $row->non_organic_waste_kg, $row->wet_maggot_kg, $row->dry_maggot_kg, $row->kasgot_kg]);
            }
            fclose($out);
        }, 'laporan-operasional-'.today()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'location_id' => ['required', 'exists:locations,id'], 'report_date' => ['required', 'date', 'before_or_equal:today'],
            'organic_waste_kg' => ['required', 'numeric', 'min:0'], 'organic_source' => ['nullable', 'string', 'max:255'], 'organic_notes' => ['nullable', 'string'],
            'non_organic_waste_kg' => ['required', 'numeric', 'min:0'], 'non_organic_type' => ['nullable', 'string', 'max:255'], 'non_organic_notes' => ['nullable', 'string'],
            'eggs_amount' => ['required', 'numeric', 'min:0'], 'eggs_unit' => ['required', Rule::in(['gram', 'kilogram', 'estimasi'])],
            'baby_maggot_kg' => ['required', 'numeric', 'min:0'], 'adult_maggot_kg' => ['required', 'numeric', 'min:0'], 'prepupa_kg' => ['required', 'numeric', 'min:0'],
            'bsf_flies_count' => ['required', 'integer', 'min:0'], 'kasgot_kg' => ['required', 'numeric', 'min:0'], 'other_fertilizer_kg' => ['required', 'numeric', 'min:0'],
            'wet_maggot_kg' => ['required', 'numeric', 'min:0'], 'dry_maggot_kg' => ['required', 'numeric', 'min:0'], 'notes' => ['nullable', 'string'],
        ]);
    }

    private function ensureVisible(Request $request, DailyReport $report): void
    {
        if ($request->user()->role === 'operator') {
            abort_unless($report->created_by === $request->user()->id, 403);
        } elseif (! $request->user()->isSuperAdmin() && $request->user()->location_id) {
            abort_unless($report->location_id === $request->user()->location_id, 403);
        }
    }
}
