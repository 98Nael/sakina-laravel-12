<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin\Admin;
use App\Models\Doctor\Doctor;
use App\Models\Patient\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'role' => 'nullable|in:admin,user,doctor,patient',
            'status' => 'nullable|in:active,suspended,deactivated',
            'sort' => 'nullable|in:latest,oldest,name_asc,name_desc,email_asc,email_desc',
        ]);

        $query = User::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['role'])) {
            $query->where('role', $validated['role']);
        }

        if (!empty($validated['status'])) {
            $query->where('account_status', $validated['status']);
        }

        $sort = $validated['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'email_asc':
                $query->orderBy('email');
                break;
            case 'email_desc':
                $query->orderByDesc('email');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $users = $query->paginate(15)->withQueryString()->through(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'account_status' => $user->account_status ?? 'active',
                'last_login_at' => $user->last_login_at?->toDateTimeString(),
                'suspended_at' => $user->suspended_at?->toDateTimeString(),
                'deactivated_at' => $user->deactivated_at?->toDateTimeString(),
                'created_at' => $user->created_at?->toDateTimeString(),
            ];
        });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'role' => $validated['role'] ?? '',
                'status' => $validated['status'] ?? '',
                'sort' => $sort,
            ],
            'stats' => [
                'total' => User::count(),
                'active' => User::where('account_status', 'active')->count(),
                'suspended' => User::where('account_status', 'suspended')->count(),
                'deactivated' => User::where('account_status', 'deactivated')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return redirect()->route('admin.users.index');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:user,admin,doctor,patient',
            'account_status' => 'nullable|in:active,suspended,deactivated',
        ]);

        $status = $validated['account_status'] ?? 'active';

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'account_status' => $status,
            'suspended_at' => $status === 'suspended' ? now() : null,
            'deactivated_at' => $status === 'deactivated' ? now() : null,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        return redirect()->route('admin.users.index');
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin,doctor,patient',
            'password' => 'nullable|string|min:8|confirmed',
            'account_status' => 'required|in:active,suspended,deactivated',
        ]);

        if ($user->id === auth()->id() && $validated['account_status'] !== 'active') {
            return back()->withErrors([
                'account_status' => 'You cannot suspend or deactivate your own account.',
            ]);
        }

        if ($user->role !== $validated['role'] && $this->hasLinkedRoleProfile($user)) {
            return back()->withErrors([
                'role' => 'Cannot change role for a user linked to admin/doctor/patient profile.',
            ]);
        }

        $oldEmail = $user->email;
        $newStatus = $validated['account_status'];
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'account_status' => $newStatus,
            'suspended_at' => $newStatus === 'suspended'
                ? ($user->suspended_at ?? now())
                : null,
            'deactivated_at' => $newStatus === 'deactivated'
                ? ($user->deactivated_at ?? now())
                : null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $this->syncRoleProfileIdentity($user, $oldEmail);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Delete the specified user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'delete' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Update user account lifecycle status.
     */
    public function updateUserStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'account_status' => 'required|in:active,suspended,deactivated',
        ]);

        if ($user->id === auth()->id() && $validated['account_status'] !== 'active') {
            return back()->withErrors([
                'account_status' => 'You cannot suspend or deactivate your own account.',
            ]);
        }

        $status = $validated['account_status'];
        $user->update([
            'account_status' => $status,
            'suspended_at' => $status === 'suspended'
                ? ($user->suspended_at ?? now())
                : null,
            'deactivated_at' => $status === 'deactivated'
                ? ($user->deactivated_at ?? now())
                : null,
        ]);

        return back()->with('success', 'Account status updated successfully.');
    }

    /**
     * Check if user has a role-linked profile row.
     */
    private function hasLinkedRoleProfile(User $user): bool
    {
        return match ($user->role) {
            'admin' => Admin::where('email', $user->email)->exists(),
            'doctor' => Doctor::where('email', $user->email)->exists(),
            'patient' => Patient::where('user_id', $user->id)->orWhere('email', $user->email)->exists(),
            default => false,
        };
    }

    /**
     * Keep role tables in sync with user name/email changes.
     */
    private function syncRoleProfileIdentity(User $user, string $oldEmail): void
    {
        if ($user->role === 'admin') {
            Admin::where('email', $oldEmail)->update([
                'name' => $user->name,
                'email' => $user->email,
            ]);

            return;
        }

        if ($user->role === 'doctor') {
            Doctor::where('email', $oldEmail)->update([
                'name' => $user->name,
                'email' => $user->email,
            ]);

            return;
        }

        if ($user->role === 'patient') {
            Patient::where('user_id', $user->id)
                ->orWhere('email', $oldEmail)
                ->update([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
        }
    }

    // ===============================================
    // ADMINS MANAGEMENT
    // ===============================================

    /**
     * Display a listing of admins
     */
    public function admins()
    {
        $admins = \App\Models\Admin\Admin::paginate(15);
        
        return Inertia::render('Admin/Admins/Index', [
            'admins' => $admins,
        ]);
    }

    /**
     * Store a newly created admin
     */
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        \App\Models\Admin\Admin::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully');
    }

    /**
     * Update the specified admin
     */
    public function updateAdmin(Request $request, \App\Models\Admin\Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $admin->update([
            ...$validated,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully');
    }

    /**
     * Delete the specified admin
     */
    public function destroyAdmin(\App\Models\Admin\Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully');
    }

    // ===============================================
    // DOCTORS MANAGEMENT
    // ===============================================

    /**
     * Display a listing of doctors
     */
    public function doctors(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'verified' => 'nullable|in:1,0',
            'specialization' => 'nullable|string|max:255',
            'hospital_name' => 'nullable|string|max:255',
            'min_experience' => 'nullable|integer|min:0',
            'max_experience' => 'nullable|integer|min:0',
            'sort' => 'nullable|in:latest,oldest,name_asc,name_desc,exp_asc,exp_desc',
        ]);

        $query = \App\Models\Doctor\Doctor::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('verified', $validated)) {
            $query->where('verified', $validated['verified'] === '1');
        }

        if (!empty($validated['specialization'])) {
            $query->where('specialization', 'like', '%' . $validated['specialization'] . '%');
        }

        if (!empty($validated['hospital_name'])) {
            $query->where('hospital_name', 'like', '%' . $validated['hospital_name'] . '%');
        }

        if (isset($validated['min_experience']) && $validated['min_experience'] !== null) {
            $query->where('years_experience', '>=', (int) $validated['min_experience']);
        }

        if (isset($validated['max_experience']) && $validated['max_experience'] !== null) {
            $query->where('years_experience', '<=', (int) $validated['max_experience']);
        }

        $sort = $validated['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'exp_asc':
                $query->orderBy('years_experience');
                break;
            case 'exp_desc':
                $query->orderByDesc('years_experience');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $doctors = $query->paginate(15)->withQueryString()->through(function (\App\Models\Doctor\Doctor $doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'license_number' => $doctor->license_number,
                'specialization' => $doctor->specialization,
                'hospital_name' => $doctor->hospital_name,
                'photo_path' => $doctor->photo_path,
                'photo_url' => $doctor->photo_path ? Storage::url($doctor->photo_path) : null,
                'verified' => (bool) $doctor->verified,
                'bio' => $doctor->bio,
                'years_experience' => $doctor->years_experience,
                'rating' => $doctor->rating,
                'created_at' => $doctor->created_at?->toDateTimeString(),
            ];
        });
        
        return Inertia::render('Admin/Doctors/Index', [
            'doctors' => $doctors,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'verified' => $validated['verified'] ?? '',
                'specialization' => $validated['specialization'] ?? '',
                'hospital_name' => $validated['hospital_name'] ?? '',
                'min_experience' => $validated['min_experience'] ?? '',
                'max_experience' => $validated['max_experience'] ?? '',
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Show the form for creating a new doctor
     */
    public function createDoctor()
    {
        return Inertia::render('Admin/Doctors/Create');
    }

    /**
     * Store a newly created doctor
     */
    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:doctors',
            'specialization' => 'required|string|max:255',
            'hospital_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'bio' => 'nullable|string',
            'years_experience' => 'nullable|integer',
        ]);

        $password = $validated['password'] ?? Str::random(16);
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('doctors', 'public')
            : null;

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => 'doctor',
        ]);

        \App\Models\Doctor\Doctor::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'license_number' => $validated['license_number'],
            'specialization' => $validated['specialization'],
            'hospital_name' => $validated['hospital_name'] ?? null,
            'photo_path' => $photoPath,
            'bio' => $validated['bio'] ?? null,
            'years_experience' => $validated['years_experience'] ?? 0,
            'verified' => false,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor created successfully');
    }

    /**
     * Display the specified doctor
     */
    public function showDoctor(\App\Models\Doctor\Doctor $doctor)
    {
        return Inertia::render('Admin/Doctors/Show', [
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'license_number' => $doctor->license_number,
                'specialization' => $doctor->specialization,
                'hospital_name' => $doctor->hospital_name,
                'photo_path' => $doctor->photo_path,
                'photo_url' => $doctor->photo_path ? Storage::url($doctor->photo_path) : null,
                'verified' => (bool) $doctor->verified,
                'bio' => $doctor->bio,
                'years_experience' => $doctor->years_experience,
                'rating' => $doctor->rating,
            ],
        ]);
    }

    /**
     * Export doctor data as printable PDF view.
     */
    public function exportDoctorPdf(\App\Models\Doctor\Doctor $doctor)
    {
        return response()
            ->view('exports.doctor', [
                'doctor' => $doctor,
            ])
            ->header('Content-Disposition', 'inline; filename="doctor-' . $doctor->id . '.pdf"');
    }

    /**
     * Update the specified doctor
     */
    public function updateDoctor(Request $request, \App\Models\Doctor\Doctor $doctor)
    {
        $oldEmail = $doctor->email;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:doctors,license_number,' . $doctor->id,
            'specialization' => 'required|string|max:255',
            'hospital_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_photo' => 'nullable|boolean',
            'bio' => 'nullable|string',
            'years_experience' => 'nullable|integer',
            'verified' => 'boolean',
        ]);

        $photoPath = $doctor->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('doctors', 'public');
        } elseif ($request->boolean('delete_photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = null;
        }

        $doctor->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'license_number' => $validated['license_number'],
            'specialization' => $validated['specialization'],
            'hospital_name' => $validated['hospital_name'] ?? null,
            'photo_path' => $photoPath,
            'bio' => $validated['bio'] ?? null,
            'years_experience' => $validated['years_experience'] ?? 0,
            'verified' => $validated['verified'] ?? false,
        ]);

        User::where('email', $oldEmail)
            ->where('role', 'doctor')
            ->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor updated successfully');
    }

    /**
     * Quick update doctor verification status.
     */
    public function updateDoctorStatus(Request $request, \App\Models\Doctor\Doctor $doctor)
    {
        $validated = $request->validate([
            'verified' => 'required|boolean',
        ]);

        $doctor->update([
            'verified' => $validated['verified'],
        ]);

        return back()->with('success', 'Doctor status updated successfully.');
    }

    /**
     * Delete the specified doctor
     */
    public function destroyDoctor(\App\Models\Doctor\Doctor $doctor)
    {
        if ($doctor->photo_path) {
            Storage::disk('public')->delete($doctor->photo_path);
        }

        User::where('email', $doctor->email)
            ->where('role', 'doctor')
            ->delete();

        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted successfully');
    }

    // ===============================================
    // PATIENTS MANAGEMENT
    // ===============================================

    /**
     * Display a listing of patients
     */
    public function patients(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,approved,rejected',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string|max:255',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'has_photo' => 'nullable|in:0,1',
            'sort' => 'nullable|in:latest,oldest,name_asc,name_desc,age_asc,age_desc',
        ]);

        $query = Patient::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['status'])) {
            $query->where('approval_status', $validated['status']);
        }

        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        if (!empty($validated['city'])) {
            $query->where('city', 'like', '%' . $validated['city'] . '%');
        }

        if (isset($validated['min_age']) && $validated['min_age'] !== null) {
            $query->where('age', '>=', (int) $validated['min_age']);
        }

        if (isset($validated['max_age']) && $validated['max_age'] !== null) {
            $query->where('age', '<=', (int) $validated['max_age']);
        }

        if (array_key_exists('has_photo', $validated)) {
            if ($validated['has_photo'] === '1') {
                $query->whereNotNull('photo_path');
            } elseif ($validated['has_photo'] === '0') {
                $query->whereNull('photo_path');
            }
        }

        $sort = $validated['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'age_asc':
                $query->orderBy('age');
                break;
            case 'age_desc':
                $query->orderByDesc('age');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $patients = $query->paginate(15)->withQueryString()->through(function (Patient $patient) {
            return [
                'id' => $patient->id,
                'user_id' => $patient->user_id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'age' => $patient->age,
                'gender' => $patient->gender,
                'city' => $patient->city,
                'approval_status' => $patient->approval_status,
                'photo_path' => $patient->photo_path,
                'photo_url' => $patient->photo_path ? Storage::url($patient->photo_path) : null,
                'created_at' => $patient->created_at?->toDateTimeString(),
            ];
        });
        
        return Inertia::render('Admin/Patients/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'status' => $validated['status'] ?? '',
                'gender' => $validated['gender'] ?? '',
                'city' => $validated['city'] ?? '',
                'min_age' => $validated['min_age'] ?? '',
                'max_age' => $validated['max_age'] ?? '',
                'has_photo' => $validated['has_photo'] ?? '',
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Create a patient directly by admin (auto approved).
     */
    public function storePatient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:patients,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('patients', 'public')
            : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
        ]);

        Patient::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'city' => $validated['city'] ?? null,
            'photo_path' => $photoPath,
            'approval_status' => 'approved',
            'created_by_user_id' => auth()->id(),
            'created_by_role' => 'admin',
            'approved_by_user_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Patient created successfully.');
    }

    /**
     * Update the specified patient.
     */
    public function updatePatient(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id . '|unique:users,email,' . ($patient->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_photo' => 'nullable|boolean',
        ]);

        $photoPath = $patient->photo_path;
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

        $patient->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'city' => $validated['city'] ?? null,
            'photo_path' => $photoPath,
        ]);

        if ($patient->user_id) {
            $userUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }

            User::whereKey($patient->user_id)->update($userUpdate);
        }

        return back()->with('success', 'Patient updated successfully.');
    }

    /**
     * Export patient data as printable PDF view.
     */
    public function exportPatientPdf(Patient $patient)
    {
        return response()
            ->view('exports.patient', [
                'patient' => $patient,
            ])
            ->header('Content-Disposition', 'inline; filename="patient-' . $patient->id . '.pdf"');
    }

    /**
     * List pending patients created by doctors.
     */
    public function pendingPatients()
    {
        $patients = Patient::where('approval_status', 'pending')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Patients/Pending', [
            'patients' => $patients,
        ]);
    }

    /**
     * Approve patient registration created by a doctor.
     */
    public function approvePatient(Patient $patient)
    {
        $patient->update([
            'approval_status' => 'approved',
            'approved_by_user_id' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => null,
        ]);

        return back()->with('success', 'Patient approved successfully.');
    }

    /**
     * Reject patient registration created by a doctor.
     */
    public function rejectPatient(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $patient->update([
            'approval_status' => 'rejected',
            'approved_by_user_id' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);

        return back()->with('success', 'Patient rejected.');
    }

    /**
     * Delete the specified patient
     */
    public function destroyPatient(Patient $patient)
    {
        if ($patient->photo_path) {
            Storage::disk('public')->delete($patient->photo_path);
        }

        if ($patient->user_id) {
            User::whereKey($patient->user_id)->delete();
        }

        $patient->delete();

        return redirect()->route('admin.patients.index')->with('success', 'Patient deleted successfully');
    }
}
