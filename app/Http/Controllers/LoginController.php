<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient\Patient;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function index()
    {
        return Inertia::render('Login');
    }

    /**
     * Store login credentials
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt($validated, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on user role
            $user = Auth::user();

            if (($user->account_status ?? 'active') !== 'active') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ]);
            }
            
            if ($user->role === 'admin') {
                $user->forceFill(['last_login_at' => now()])->save();
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'doctor') {
                $user->forceFill(['last_login_at' => now()])->save();
                return redirect()->route('doctor.dashboard');
            } elseif ($user->role === 'patient') {
                $patient = Patient::where('email', $user->email)->first();

                if (!$patient || $patient->approval_status !== 'approved') {
                    Auth::logout();

                    return back()->withErrors([
                        'email' => 'Your patient account is pending admin approval.',
                    ]);
                }

                $user->forceFill(['last_login_at' => now()])->save();
                return redirect()->route('patient.dashboard');
            }

            $user->forceFill(['last_login_at' => now()])->save();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
