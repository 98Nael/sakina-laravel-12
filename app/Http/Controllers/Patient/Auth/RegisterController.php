<?php

namespace App\Http\Controllers\Patient\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Patient\Patient;

class RegisterController extends Controller
{
    /**
     * Show patient registration form
     */
    public function index()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle patient registration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string|max:255',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
        ]);

        // Create patient profile
        Patient::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'city' => $validated['city'] ?? null,
            'approval_status' => 'approved',
            'created_by_user_id' => $user->id,
            'created_by_role' => 'patient',
            'approved_by_user_id' => $user->id,
            'approved_at' => now(),
        ]);

        // Login the user
        Auth::login($user);

        return redirect()->route('patient.dashboard');
    }
}
