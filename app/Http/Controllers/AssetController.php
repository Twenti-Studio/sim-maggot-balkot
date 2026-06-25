<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with('location')->latest();
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')->orWhere('asset_code', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        return view('assets.index', ['assets' => $query->paginate(15)->withQueryString()]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->hasPermission('asset.create'), 403);

        return view('assets.form', ['asset' => new Asset, 'locations' => Location::where('is_active', true)->get()]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('asset.create'), 403);
        $asset = Asset::create($this->validated($request) + ['created_by' => $request->user()->id]);
        AuditLog::record('asset.created', $asset);

        return redirect()->route('assets.show', $asset)->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['location', 'maintenances.assignedStaff']);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset, Request $request)
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin'], true), 403);

        return view('assets.form', ['asset' => $asset, 'locations' => Location::where('is_active', true)->get()]);
    }

    public function update(Request $request, Asset $asset)
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin'], true), 403);
        $old = $asset->toArray();
        $asset->update($this->validated($request, $asset));
        AuditLog::record('asset.updated', $asset, $old, $asset->fresh()->toArray());

        return redirect()->route('assets.show', $asset)->with('success', 'Data aset diperbarui.');
    }

    public function updateCondition(Request $request, Asset $asset)
    {
        $data = $request->validate(['condition' => ['required', Rule::in(['good', 'needs_maintenance', 'minor_damage', 'major_damage'])], 'notes' => ['nullable', 'string']]);
        $old = $asset->only(['condition', 'notes']);
        $asset->update($data);
        AuditLog::record('asset.condition_updated', $asset, $old, $data);

        return back()->with('success', 'Kondisi aset berhasil dilaporkan.');
    }

    public function destroy(Asset $asset, Request $request)
    {
        abort_unless($request->user()->hasPermission('asset.delete'), 403);
        $asset->update(['status' => 'archived']);
        $asset->delete();
        AuditLog::record('asset.archived', $asset);

        return redirect()->route('assets.index')->with('success', 'Aset diarsipkan.');
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        return $request->validate([
            'location_id' => ['required', 'exists:locations,id'], 'asset_code' => ['required', 'string', 'max:50', Rule::unique('assets')->ignore($asset?->id)],
            'name' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:100'], 'location_detail' => ['nullable', 'string', 'max:255'],
            'purchased_at' => ['nullable', 'date'], 'purchase_value' => ['required', 'numeric', 'min:0'],
            'condition' => ['required', Rule::in(['good', 'needs_maintenance', 'minor_damage', 'major_damage'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'archived'])], 'notes' => ['nullable', 'string'],
        ]);
    }
}
