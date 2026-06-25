<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2000'], 'keys.p256dh' => ['required', 'string'], 'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'in:aesgcm,aes128gcm'], 'device_name' => ['nullable', 'string', 'max:255'],
        ]);
        $hash = hash('sha256', $data['endpoint']);
        $request->user()->pushSubscriptions()->updateOrCreate(['endpoint_hash' => $hash], [
            'endpoint' => $data['endpoint'], 'public_key' => $data['keys']['p256dh'], 'auth_token' => $data['keys']['auth'],
            'content_encoding' => $data['contentEncoding'] ?? 'aes128gcm', 'device_name' => $data['device_name'] ?? $request->userAgent(), 'is_active' => true,
        ]);

        return response()->json(['message' => 'Push notification aktif.']);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['endpoint' => ['required', 'url']]);
        $request->user()->pushSubscriptions()->where('endpoint_hash', hash('sha256', $data['endpoint']))->update(['is_active' => false]);

        return response()->json(['message' => 'Push notification dinonaktifkan.']);
    }
}
