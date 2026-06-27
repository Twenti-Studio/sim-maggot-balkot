<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WasteTypeController extends Controller
{
    public function index()
    {
        return view('waste-types.index', [
            'types' => WasteType::orderBy('category')->orderBy('name')->paginate(20),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $type = WasteType::create([...$data, 'created_by' => $request->user()->id]);
        AuditLog::record('waste_type.created', $type, [], $type->toArray());

        return back()->with('success', 'Jenis sampah berhasil ditambahkan.');
    }

    public function update(Request $request, WasteType $wasteType)
    {
        $data = $this->validated($request, $wasteType);
        $old = $wasteType->toArray();
        $wasteType->update($data);
        AuditLog::record('waste_type.updated', $wasteType, $old, $wasteType->fresh()->toArray());

        return back()->with('success', 'Jenis sampah berhasil diperbarui.');
    }

    private function validated(Request $request, ?WasteType $wasteType = null): array
    {
        return $request->validate([
            'category' => ['required', 'string', Rule::in($this->categories())],
            'name' => ['required', 'string', 'max:255', Rule::unique('waste_types')->ignore($wasteType?->id)],
            'unit' => ['required', 'string', 'max:20'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    private function categories(): array
    {
        return ['Organik', 'Non-organik', 'Daur ulang', 'Residu', 'B3 terbatas'];
    }
}
