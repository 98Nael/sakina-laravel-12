<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show doctor profile page.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $doctor = Doctor::where('email', $user->email)->first();

        return Inertia::render('Profile', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $doctor?->phone ?? '',
                'specialization' => $doctor?->specialization ?? '',
                'license_number' => $doctor?->license_number ?? '',
                'hospital_name' => $doctor?->hospital_name ?? '',
                'years_experience' => $doctor?->years_experience ?? 0,
                'bio' => $doctor?->bio ?? '',
            ],
        ]);
    }

    /**
     * Update doctor profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $doctor = Doctor::where('email', $user->email)->first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('doctors', 'email')->ignore($doctor?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('doctors', 'license_number')->ignore($doctor?->id),
            ],
            'hospital_name' => ['nullable', 'string', 'max:255'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldEmail = $user->email;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $doctorProfile = $doctor ?: Doctor::where('email', $oldEmail)->first();
        if (!$doctorProfile) {
            $doctorProfile = new Doctor();
        }

        $doctorProfile->name = $validated['name'];
        $doctorProfile->email = $validated['email'];
        $doctorProfile->phone = $validated['phone'] ?? null;
        $doctorProfile->specialization = $validated['specialization'];
        $doctorProfile->license_number = $validated['license_number'];
        $doctorProfile->hospital_name = $validated['hospital_name'] ?? null;
        $doctorProfile->years_experience = $validated['years_experience'] ?? 0;
        $doctorProfile->bio = $validated['bio'] ?? null;
        $doctorProfile->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
