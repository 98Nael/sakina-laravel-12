<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PatientController extends Controller
{
    /**
     * Display a listing of doctor's patients
     */
    public function index(Request $request)
    {
        $patients = Patient::query()
                        ->latest()
                        ->paginate(15);
        
        return Inertia::render('Patients/Index', [
            'patients' => $patients,
        ]);
    }

    /**
     * Doctor creates a new patient account pending admin approval.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:patients,email',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $password = $validated['password'] ?? Str::random(16);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
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
            'approval_status' => 'pending',
            'created_by_user_id' => $request->user()->id,
            'created_by_role' => 'doctor',
        ]);

        return back()->with('success', 'Patient created and sent for admin approval.');
    }

    /**
     * Show patient details
     */
    public function show(User $patient)
    {
        if ($patient->role !== 'patient') {
            abort(403, 'This user is not a patient');
        }

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'medical_history' => [], // Add real data
            'appointments' => [], // Add real data
        ]);
    }

    /**
     * Add notes to patient
     */
    public function addNote(Request $request, User $patient)
    {
        $validated = $request->validate([
            'note' => 'required|string',
            'type' => 'required|in:diagnosis,treatment,observation',
        ]);

        // Save note to database
        
        return back()->with('success', 'Note added successfully');
    }

    /**
     * View patient medical history
     */
    public function medicalHistory(User $patient)
    {
        return Inertia::render('Patients/MedicalHistory', [
            'patient' => $patient,
            'history' => [], // Add real data
        ]);
    }

    /**
     * View patient medical records
     */
    public function records(User $patient)
    {
        $records = \App\Models\MedicalRecord::where('patient_id', $patient->id)
                    ->where('doctor_id', auth()->id())
                    ->latest()
                    ->get();

        return Inertia::render('Patients/Records', [
            'patient' => $patient,
            'records' => $records,
        ]);
    }

    /**
     * Create patient medical record
     */
    public function createRecord(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'symptoms' => 'nullable|array',
            'test_results' => 'nullable|array',
        ]);

        \App\Models\MedicalRecord::create([
            ...$validated,
            'doctor_id' => auth()->id(),
            'record_date' => now(),
        ]);

        return back()->with('success', 'Medical record created successfully');
    }
}
