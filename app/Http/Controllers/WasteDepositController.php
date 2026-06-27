<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\WasteDeposit;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WasteDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = WasteDeposit::with(['user', 'location', 'wasteType', 'creator'])->latest('created_at');
        if ($request->user()->role === 'user') {
            $query->where('user_id', $request->user()->id);
        } elseif ($request->user()->role === 'operator') {
            $query->where('created_by', $request->user()->id);
        } elseif (! $request->user()->isSuperAdmin() && $request->user()->location_id) {
            $query->where('location_id', $request->user()->location_id);
        }

        return view('waste-deposits.index', [
            'deposits' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(Request $request)
    {
        $users = User::with('location')
            ->where('role', 'user')
            ->orderBy('name')
            ->get();

        return view('waste-deposits.form', [
            'users' => $users,
            'types' => WasteType::where('is_active', true)->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'deposit_date' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array'],
            'items.*.selected' => ['nullable', 'boolean'],
            'items.*.weight' => ['nullable', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = User::where('role', 'user')->findOrFail($data['user_id']);
        abort_unless($user->location_id, 422, 'Akun user belum memiliki lokasi maggot.');

        $selected = collect($data['items'])
            ->filter(fn ($item) => ! empty($item['selected']) && isset($item['weight']) && (float) $item['weight'] > 0);

        if ($selected->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Pilih minimal satu jenis sampah dan isi beratnya.']);
        }

        $types = WasteType::where('is_active', true)
            ->whereIn('id', $selected->keys())
            ->get()
            ->keyBy('id');

        $deposits = DB::transaction(function () use ($selected, $types, $user, $data, $request) {
            return $selected->map(function ($item, $typeId) use ($types, $user, $data, $request) {
                $type = $types->get((int) $typeId);
                if (! $type) {
                    throw ValidationException::withMessages(['items' => 'Jenis sampah yang dipilih tidak aktif atau tidak tersedia.']);
                }

                $weight = (float) $item['weight'];
                $deposit = WasteDeposit::create([
                    'user_id' => $user->id,
                    'location_id' => $user->location_id,
                    'waste_type_id' => $type->id,
                    'deposit_date' => $data['deposit_date'],
                    'weight' => $weight,
                    'price_per_unit' => $type->price_per_unit,
                    'total_amount' => $weight * (float) $type->price_per_unit,
                    'notes' => $data['notes'] ?? null,
                    'created_by' => $request->user()->id,
                ]);
                AuditLog::record('waste_deposit.created', $deposit, [], $deposit->toArray());

                return $deposit;
            });
        });

        return redirect()->route('waste-deposits.index')->with('success', $deposits->count().' setoran sampah berhasil dicatat. Riwayat tabungan user '.$user->name.' sudah diperbarui.');
    }
}
