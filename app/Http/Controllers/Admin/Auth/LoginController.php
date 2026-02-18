<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the admin login form
     */
    public function index()
    {
        return Inertia::render('Auth/Login')->rootView('Admin.app');
    }

    /**
     * Store login credentials (admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt($validated, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (($user->account_status ?? 'active') !== 'active') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ]);
            }

            // Ensure it's an admin
            if ($user->role === 'admin') {
                $user->forceFill(['last_login_at' => now()])->save();
                return redirect()->route('admin.dashboard');
            }

            // If not an admin, logout
            Auth::logout();
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not an admin account.',
        ]);
    }

    /**
     * Log the admin out
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
