<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\MaintenanceRecord;
use App\Models\WasteDeposit;
use App\Models\WasteWithdrawal;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $reports = DailyReport::query()->where('status', 'validated');
        if ($user->role === 'operator') {
            $reports->where('created_by', $user->id);
        } elseif (! $user->isSuperAdmin() && $user->location_id) {
            $reports->where('location_id', $user->location_id);
        }

        $monthReports = (clone $reports)->whereBetween('report_date', [now()->startOfMonth(), now()->endOfMonth()]);
        $depositQuery = WasteDeposit::query();
        $withdrawalQuery = WasteWithdrawal::query()->whereIn('status', ['submitted', 'completed']);
        if ($user->role === 'user') {
            $depositQuery->where('user_id', $user->id);
            $withdrawalQuery->where('user_id', $user->id);
        } elseif (! $user->isSuperAdmin() && $user->location_id) {
            $depositQuery->where('location_id', $user->location_id);
            $withdrawalQuery->where('location_id', $user->location_id);
        }

        $totalDeposits = (clone $depositQuery)->sum('total_amount');
        $totalWithdrawals = (clone $withdrawalQuery)->sum('amount');
        $summary = [
            'organic' => (clone $monthReports)->sum('organic_waste_kg'),
            'non_organic' => (clone $monthReports)->sum('non_organic_waste_kg'),
            'wet_maggot' => (clone $monthReports)->sum('wet_maggot_kg'),
            'kasgot' => (clone $monthReports)->sum('kasgot_kg'),
            'savings' => max(0, $totalDeposits - $totalWithdrawals),
            'attendance' => Attendance::whereDate('attendance_date', today())->whereNotNull('check_in_at')->count(),
            'critical_assets' => Asset::whereIn('condition', ['needs_maintenance', 'major_damage'])->count(),
        ];

        if ($user->role === 'user') {
            return view('dashboard', [
                'summary' => [
                    'savings' => $summary['savings'],
                    'deposits' => $totalDeposits,
                    'withdrawals' => $totalWithdrawals,
                    'weight' => (clone $depositQuery)->sum('weight'),
                    'transactions' => (clone $depositQuery)->count(),
                ],
                'recentDeposits' => WasteDeposit::with(['wasteType', 'creator'])
                    ->where('user_id', $user->id)
                    ->latest('created_at')
                    ->limit(5)
                    ->get(),
                'location' => $user->location,
                'pendingReports' => collect(),
                'dueMaintenances' => collect(),
            ]);
        }

        $chart = collect(CarbonPeriod::create(now()->subDays(6), now()))->map(function ($day) use ($reports) {
            $row = (clone $reports)->whereDate('report_date', $day)->first();

            return ['label' => $day->translatedFormat('D'), 'organic' => (float) ($row?->organic_waste_kg ?? 0), 'production' => (float) ($row?->wet_maggot_kg ?? 0)];
        });

        return view('dashboard', [
            'summary' => $summary,
            'chart' => $chart,
            'pendingReports' => $user->hasPermission('production.validate') ? DailyReport::with('creator')->where('status', 'submitted')->latest('report_date')->limit(5)->get() : collect(),
            'dueMaintenances' => MaintenanceRecord::with('asset')->whereIn('status', ['scheduled', 'in_progress'])->whereDate('scheduled_at', '<=', now()->addDays(7))->orderBy('scheduled_at')->limit(5)->get(),
        ]);
    }
}
