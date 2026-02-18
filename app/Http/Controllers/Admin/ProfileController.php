<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show admin profile page.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('email', $user->email)->first();

        return Inertia::render('Admin/Profile', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $admin?->phone ?? '',
                'position' => $admin?->position ?? 'Admin',
            ],
        ]);
    }

    /**
     * Update admin profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('email', $user->email)->first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('admins', 'email')->ignore($admin?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldEmail = $user->email;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $adminProfile = $admin ?: Admin::where('email', $oldEmail)->first();
        if (!$adminProfile) {
            $adminProfile = new Admin();
            $adminProfile->created_by = null;
        }

        $adminProfile->name = $validated['name'];
        $adminProfile->email = $validated['email'];
        $adminProfile->phone = $validated['phone'] ?? null;
        $adminProfile->position = $validated['position'] ?? 'Admin';
        $adminProfile->updated_by = null;
        $adminProfile->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}

