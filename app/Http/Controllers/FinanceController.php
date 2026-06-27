<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FinancialTransaction;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['location', 'creator', 'validator'])->latest('transaction_date');
        $this->applyFilters($query, $request);

        $summaryQuery = FinancialTransaction::query()->where('status', 'validated');
        $this->applyFilters($summaryQuery, $request, includeStatus: false);
        $income = (clone $summaryQuery)->whereIn('type', ['cash_in', 'cashier_sale'])->sum('amount');
        $expense = (clone $summaryQuery)->where('type', 'cash_out')->sum('amount');

        return view('finance.index', [
            'transactions' => $query->paginate(15)->withQueryString(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'summary' => [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
            ],
        ]);
    }

    public function create()
    {
        return view('finance.form', [
            'transaction' => new FinancialTransaction(['transaction_date' => today(), 'status' => 'draft']),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->normalizeStatus($data, $request);
        $data['created_by'] = $request->user()->id;
        $transaction = FinancialTransaction::create($data);
        AuditLog::record('finance.created', $transaction);

        return redirect()->route('finance.index')->with('success', 'Transaksi keuangan berhasil disimpan.');
    }

    public function edit(FinancialTransaction $transaction)
    {
        return view('finance.form', [
            'transaction' => $transaction,
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, FinancialTransaction $transaction)
    {
        $old = $transaction->toArray();
        $transaction->update($this->normalizeStatus($this->validated($request), $request));
        AuditLog::record('finance.updated', $transaction, $old, $transaction->fresh()->toArray());

        return redirect()->route('finance.index')->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    public function validateTransaction(FinancialTransaction $transaction, Request $request)
    {
        abort_unless($request->user()->hasPermission('finance.validate'), 403);
        $transaction->update([
            'status' => 'validated',
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);
        AuditLog::record('finance.validated', $transaction);

        return back()->with('success', 'Transaksi keuangan berhasil divalidasi.');
    }

    public function destroy(FinancialTransaction $transaction)
    {
        $transaction->delete();
        AuditLog::record('finance.archived', $transaction);

        return back()->with('success', 'Transaksi keuangan diarsipkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'],
            'transaction_date' => ['required', 'date'],
            'type' => ['required', Rule::in(['cash_in', 'cashier_sale', 'cash_out'])],
            'category' => ['nullable', 'string', 'max:100'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'counterparty' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['draft', 'validated', 'void'])],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function applyFilters($query, Request $request, bool $includeStatus = true): void
    {
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($includeStatus && $request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date('date_to'));
        }
    }

    private function normalizeStatus(array $data, Request $request): array
    {
        if ($data['status'] === 'validated') {
            if (! $request->user()->hasPermission('finance.validate')) {
                $data['status'] = 'draft';
                return $data;
            }

            $data['validated_by'] = $request->user()->id;
            $data['validated_at'] = now();

            return $data;
        }

        $data['validated_by'] = null;
        $data['validated_at'] = null;

        return $data;
    }
}
