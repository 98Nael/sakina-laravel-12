<?php

namespace App\Http\Controllers\Patient\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Show the patient login form
     */
    public function index()
    {
        return Inertia::render('Auth/Login')->rootView('Patient.app');
    }

    /**
     * Store login credentials (patient)
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

            // Ensure it's a patient
            if ($user->role === 'patient') {
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

            // If not a patient, logout
            Auth::logout();
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not a patient account.',
        ]);
    }

    /**
     * Log the patient out
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('patient.login');
    }
}
