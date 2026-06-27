<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register', [
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($request->user()->role === 'user' ? route('savings.index') : route('dashboard'));
    }

    public function storeRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'location_id' => ['required', 'exists:locations,id'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            ...$data,
            'role' => 'user',
            'is_active' => true,
        ]);

        AuditLog::record('user.registered', $user, [], $user->only(['name', 'email', 'role', 'location_id', 'is_active']));
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('savings.index')->with('success', 'Pendaftaran berhasil. Akun Anda terhubung dengan lokasi maggot terdekat.');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
