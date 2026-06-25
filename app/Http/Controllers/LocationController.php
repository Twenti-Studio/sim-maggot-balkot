<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        return view('locations.index', ['locations' => Location::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:30', 'unique:locations'], 'name' => ['required', 'string', 'max:255'], 'address' => ['nullable', 'string'], 'is_active' => ['required', 'boolean']]);
        $location = Location::create($data);
        AuditLog::record('location.created', $location);

        return back()->with('success', 'Lokasi ditambahkan.');
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:30', Rule::unique('locations')->ignore($location->id)], 'name' => ['required', 'string', 'max:255'], 'address' => ['nullable', 'string'], 'is_active' => ['required', 'boolean']]);
        $old = $location->toArray();
        $location->update($data);
        AuditLog::record('location.updated', $location, $old, $location->fresh()->toArray());

        return back()->with('success', 'Lokasi diperbarui.');
    }
}
