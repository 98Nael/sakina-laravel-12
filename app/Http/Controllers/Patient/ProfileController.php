<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show patient profile page.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $patient = Patient::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        return Inertia::render('Profile', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $patient?->phone ?? '',
                'age' => $patient?->age,
                'gender' => $patient?->gender ?? '',
                'city' => $patient?->city ?? '',
                'address' => $patient?->address ?? '',
                'blood_type' => $patient?->blood_type ?? '',
                'emergency_contact' => $patient?->emergency_contact ?? '',
                'photo_path' => $patient?->photo_path ?? null,
                'photo_url' => $patient?->photo_path ? Storage::url($patient->photo_path) : null,
            ],
        ]);
    }

    /**
     * Update patient profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $patient = Patient::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('patients', 'email')->ignore($patient?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'gender' => ['nullable', 'in:male,female,other'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delete_photo' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $name = $validated['name'] ?? $user->name;
        $email = $validated['email'] ?? $user->email;
        $oldEmail = $user->email;

        $user->name = $name;
        $user->email = $email;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $patientProfile = $patient ?: Patient::where('email', $oldEmail)->first();
        if (!$patientProfile) {
            $patientProfile = new Patient();
            $patientProfile->user_id = $user->id;
            $patientProfile->approval_status = 'approved';
        }

        $photoPath = $patientProfile->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('patients', 'public');
        } elseif ($request->boolean('delete_photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = null;
        }

        $patientProfile->user_id = $user->id;
        $patientProfile->name = $name;
        $patientProfile->email = $email;
        $patientProfile->phone = array_key_exists('phone', $validated) ? ($validated['phone'] ?? null) : $patientProfile->phone;
        $patientProfile->age = array_key_exists('age', $validated) ? ($validated['age'] ?? null) : $patientProfile->age;
        $patientProfile->gender = array_key_exists('gender', $validated) ? ($validated['gender'] ?? null) : $patientProfile->gender;
        $patientProfile->city = array_key_exists('city', $validated) ? ($validated['city'] ?? null) : $patientProfile->city;
        $patientProfile->address = array_key_exists('address', $validated) ? ($validated['address'] ?? null) : $patientProfile->address;
        $patientProfile->blood_type = array_key_exists('blood_type', $validated) ? ($validated['blood_type'] ?? null) : $patientProfile->blood_type;
        $patientProfile->emergency_contact = array_key_exists('emergency_contact', $validated) ? ($validated['emergency_contact'] ?? null) : $patientProfile->emergency_contact;
        $patientProfile->photo_path = $photoPath;
        $patientProfile->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
