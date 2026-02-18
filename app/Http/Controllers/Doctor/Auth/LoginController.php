<?php

namespace App\Http\Controllers\Doctor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the doctor login form
     */
    public function index()
    {
        return Inertia::render('Auth/Login')->rootView('Doctor.app');
    }

    /**
     * Store login credentials (doctor)
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

            // Ensure it's a doctor
            if ($user->role === 'doctor') {
                $user->forceFill(['last_login_at' => now()])->save();
                return redirect()->route('doctor.dashboard');
            }

            // If not a doctor, logout
            Auth::logout();
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not a doctor account.',
        ]);
    }

    /**
     * Log the doctor out
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('doctor.login');
    }
}
