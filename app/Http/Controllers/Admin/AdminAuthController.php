<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function create()
    {
        if (Auth::guard('admin')->check()) return redirect()->route('admin.dashboard');
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);

        if (!Auth::guard('admin')->attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'Invalid administrator credentials.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $admin = Auth::guard('admin')->user();

        if ($admin->status !== 'active') {
            Auth::guard('admin')->logout();
            return back()->withErrors(['username' => 'This administrator account is inactive.']);
        }

        $admin->forceFill(['last_login_at' => now()])->save();

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
