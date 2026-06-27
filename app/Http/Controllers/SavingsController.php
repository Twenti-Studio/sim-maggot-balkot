<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\WasteDeposit;
use App\Models\WasteType;
use App\Models\WasteWithdrawal;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    public function index(Request $request)
    {
        $summary = $this->summary($request->user()->id);

        return view('savings.index', [
            ...$summary,
            'totalWeight' => WasteDeposit::where('user_id', $request->user()->id)->sum('weight'),
            'location' => $request->user()->location,
            'wasteTypes' => WasteType::where('is_active', true)->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function history(Request $request)
    {
        $deposits = WasteDeposit::with(['wasteType', 'location', 'creator'])
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(20);

        return view('savings.history', [
            'deposits' => $deposits,
            ...$this->summary($request->user()->id),
        ]);
    }

    public function withdrawals(Request $request)
    {
        $withdrawals = WasteWithdrawal::with('location')
            ->where('user_id', $request->user()->id)
            ->latest('withdrawal_date')
            ->paginate(20);

        return view('savings.withdrawals', [
            'withdrawals' => $withdrawals,
            'location' => $request->user()->location,
            ...$this->summary($request->user()->id),
        ]);
    }

    public function withdraw(Request $request)
    {
        $user = $request->user();
        abort_unless($user->location_id, 422, 'Lokasi maggot akun Anda belum diatur.');

        $totalDeposits = WasteDeposit::where('user_id', $user->id)->sum('total_amount');
        $totalWithdrawals = WasteWithdrawal::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'completed'])
            ->sum('amount');
        $availableBalance = max(0, $totalDeposits - $totalWithdrawals);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000', 'max:'.$availableBalance],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.max' => 'Nominal tarik saldo tidak boleh melebihi saldo tersedia.',
            'amount.min' => 'Minimal penarikan saldo adalah Rp1.000.',
        ]);

        $withdrawal = WasteWithdrawal::create([
            'user_id' => $user->id,
            'location_id' => $user->location_id,
            'withdrawal_date' => today(),
            'amount' => $data['amount'],
            'status' => 'submitted',
            'notes' => $data['notes'] ?? null,
        ]);

        AuditLog::record('waste_withdrawal.submitted', $withdrawal, [], $withdrawal->toArray());

        return redirect()->route('savings.withdrawals')->with('success', 'Permintaan tarik saldo berhasil dikirim. Silakan datang ke '.$user->location->name.' untuk verifikasi saldo dan pencairan manual oleh petugas.');
    }

    private function summary(int $userId): array
    {
        $totalDeposits = WasteDeposit::where('user_id', $userId)->sum('total_amount');
        $totalWithdrawals = WasteWithdrawal::where('user_id', $userId)
            ->whereIn('status', ['submitted', 'completed'])
            ->sum('amount');

        return [
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalSavings' => max(0, $totalDeposits - $totalWithdrawals),
        ];
    }
}
