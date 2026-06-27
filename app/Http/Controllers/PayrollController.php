<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PayrollRecord;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollRecord::with(['staff.location', 'creator'])->latest('period_end');
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->integer('staff_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('payroll.index', [
            'payrollRecords' => $query->paginate(15)->withQueryString(),
            'staffMembers' => Staff::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('payroll.form', [
            'payroll' => new PayrollRecord,
            'staffMembers' => Staff::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['total_paid'] = $this->totalPaid($data);
        $payroll = PayrollRecord::create($data);
        AuditLog::record('payroll.created', $payroll);

        return redirect()->route('payroll.index')->with('success', 'Data gaji dan bonus berhasil disimpan.');
    }

    public function edit(PayrollRecord $payroll)
    {
        return view('payroll.form', [
            'payroll' => $payroll,
            'staffMembers' => Staff::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PayrollRecord $payroll)
    {
        $old = $payroll->toArray();
        $data = $this->validated($request);
        $data['total_paid'] = $this->totalPaid($data);
        $payroll->update($data);
        AuditLog::record('payroll.updated', $payroll, $old, $payroll->fresh()->toArray());

        return redirect()->route('payroll.index')->with('success', 'Data gaji dan bonus berhasil diperbarui.');
    }

    public function destroy(PayrollRecord $payroll)
    {
        $payroll->delete();
        AuditLog::record('payroll.archived', $payroll);

        return back()->with('success', 'Data gaji dan bonus diarsipkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'bonus' => ['required', 'numeric', 'min:0'],
            'deductions' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'paid', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function totalPaid(array $data): float
    {
        return max(0, (float) $data['base_salary'] + (float) $data['bonus'] - (float) $data['deductions']);
    }
}
