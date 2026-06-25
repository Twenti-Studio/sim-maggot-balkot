<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\MaintenanceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ReportExportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeOperationalExport($request);

        return view('exports.index', [
            'counts' => [
                'production' => DailyReport::count(),
                'attendance' => Attendance::count(),
                'assets' => Asset::count(),
                'maintenance' => MaintenanceRecord::count(),
            ],
        ]);
    }

    public function download(Request $request, string $type, string $format)
    {
        $this->authorizeOperationalExport($request);
        validator(compact('type', 'format'), [
            'type' => ['required', Rule::in(['production', 'attendance', 'assets', 'maintenance'])],
            'format' => ['required', Rule::in(['pdf', 'xlsx'])],
        ])->validate();

        $dataset = $this->dataset($type);
        $filename = $type.'-'.now()->format('Ymd-His');

        if ($format === 'pdf') {
            return Pdf::loadView('exports.pdf', $dataset + ['generatedBy' => $request->user()])
                ->setPaper('a4', 'landscape')
                ->download($filename.'.pdf');
        }

        $directory = storage_path('app/private/exports');
        File::ensureDirectoryExists($directory);
        $path = $directory.'/'.$filename.'.xlsx';
        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues([$dataset['title']]));
        $writer->addRow(Row::fromValues(['Dibuat', now()->format('d/m/Y H:i'), 'Oleh', $request->user()->name]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues($dataset['headers']));
        foreach ($dataset['rows'] as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        $writer->close();

        return response()->download($path, $filename.'.xlsx')->deleteFileAfterSend(true);
    }

    private function authorizeOperationalExport(Request $request): void
    {
        abort_unless($request->user()->hasPermission('report.export') && in_array($request->user()->role, ['super_admin', 'admin'], true), 403);
    }

    private function dataset(string $type): array
    {
        return match ($type) {
            'production' => [
                'title' => 'Laporan Produksi dan Sampah Rumah Maggot',
                'headers' => ['Tanggal', 'Lokasi', 'Petugas', 'Status', 'Organik (kg)', 'Non-organik (kg)', 'Telur', 'Bayi (kg)', 'Dewasa (kg)', 'Pre-pupa (kg)', 'Lalat BSF', 'Kasgot (kg)', 'Pupuk lain (kg)', 'Maggot basah (kg)', 'Maggot kering (kg)'],
                'rows' => DailyReport::with(['location', 'creator'])->orderBy('report_date')->get()->map(fn ($row) => [
                    $row->report_date->format('d/m/Y'), $row->location->name, $row->creator->name, $row->status,
                    (float) $row->organic_waste_kg, (float) $row->non_organic_waste_kg, $row->eggs_amount.' '.$row->eggs_unit,
                    (float) $row->baby_maggot_kg, (float) $row->adult_maggot_kg, (float) $row->prepupa_kg, $row->bsf_flies_count,
                    (float) $row->kasgot_kg, (float) $row->other_fertilizer_kg, (float) $row->wet_maggot_kg, (float) $row->dry_maggot_kg,
                ])->all(),
            ],
            'attendance' => [
                'title' => 'Laporan Absensi Petugas',
                'headers' => ['Tanggal', 'Kode', 'Nama Petugas', 'Lokasi', 'Check-in', 'Check-out', 'Status', 'Catatan'],
                'rows' => Attendance::with('staff.location')->orderBy('attendance_date')->get()->map(fn ($row) => [
                    $row->attendance_date->format('d/m/Y'), $row->staff->employee_code, $row->staff->name, $row->staff->location->name,
                    $row->check_in_at?->format('H:i') ?: '-', $row->check_out_at?->format('H:i') ?: '-', $row->status, $row->notes ?: '-',
                ])->all(),
            ],
            'assets' => [
                'title' => 'Laporan Inventaris Aset',
                'headers' => ['Kode', 'Nama Aset', 'Kategori', 'Lokasi', 'Detail Lokasi', 'Tanggal Beli', 'Nilai', 'Kondisi', 'Status'],
                'rows' => Asset::with('location')->orderBy('asset_code')->get()->map(fn ($row) => [
                    $row->asset_code, $row->name, $row->category, $row->location->name, $row->location_detail ?: '-',
                    $row->purchased_at?->format('d/m/Y') ?: '-', (float) $row->purchase_value, $row->condition, $row->status,
                ])->all(),
            ],
            'maintenance' => [
                'title' => 'Laporan Perawatan Aset',
                'headers' => ['Jadwal', 'Tanggal Selesai', 'Kode Aset', 'Nama Aset', 'Jenis Perawatan', 'Petugas', 'Status', 'Estimasi Biaya', 'Biaya Aktual', 'Tindakan', 'Komponen'],
                'rows' => MaintenanceRecord::with(['asset', 'assignedStaff'])->orderBy('scheduled_at')->get()->map(fn ($row) => [
                    $row->scheduled_at->format('d/m/Y'), $row->completed_at?->format('d/m/Y') ?: '-', $row->asset->asset_code, $row->asset->name,
                    $row->maintenance_type, $row->assignedStaff?->name ?: '-', $row->status, (float) $row->estimated_cost, (float) $row->actual_cost,
                    $row->actions ?: '-', $row->components ?: '-',
                ])->all(),
            ],
        };
    }
}
