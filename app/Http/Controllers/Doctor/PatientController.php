<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor\Doctor;
use App\Models\MedicalRecord;
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
        $doctorUserId = $request->user()->id;
        $doctorId = $this->resolveDoctorId($request);
        $patientIdsFromAppointments = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->pluck('patient_id');

        $patients = Patient::query()
            ->where(function ($query) use ($doctorUserId, $patientIdsFromAppointments) {
                $query->where('created_by_user_id', $doctorUserId);
                if ($patientIdsFromAppointments->isNotEmpty()) {
                    $query->orWhereIn('id', $patientIdsFromAppointments);
                }
            })
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
    public function show(Request $request, Patient $patient)
    {
        $doctorId = $this->resolveDoctorId($request);
        if (!$this->canAccessPatient($request, $patient, $doctorId)) {
            abort(403, 'You are not allowed to access this patient.');
        }

        $medicalHistory = MedicalRecord::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->latest('record_date')
            ->limit(5)
            ->get(['id', 'diagnosis', 'treatment', 'record_date', 'notes']);

        $appointments = Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->latest('date_time')
            ->limit(5)
            ->get(['id', 'date_time', 'status', 'reason']);

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'medical_history' => $medicalHistory,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Add notes to patient
     */
    public function addNote(Request $request, Patient $patient)
    {
        $doctorId = $this->resolveDoctorId($request);
        if (!$this->canAccessPatient($request, $patient, $doctorId)) {
            abort(403, 'You are not allowed to access this patient.');
        }

        $validated = $request->validate([
            'note' => 'required|string',
            'type' => 'required|in:diagnosis,treatment,observation',
        ]);

        $diagnosis = $validated['type'] === 'diagnosis' ? $validated['note'] : 'General follow-up';
        $treatment = $validated['type'] === 'treatment' ? $validated['note'] : 'Observation';

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorId,
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
            'record_date' => now(),
            'notes' => $validated['note'],
            'is_confidential' => false,
        ]);
        
        return back()->with('success', 'Note added successfully');
    }

    /**
     * View patient medical history
     */
    public function medicalHistory(Request $request, Patient $patient)
    {
        $doctorId = $this->resolveDoctorId($request);
        if (!$this->canAccessPatient($request, $patient, $doctorId)) {
            abort(403, 'You are not allowed to access this patient.');
        }

        $history = MedicalRecord::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->latest('record_date')
            ->get();

        return Inertia::render('Patients/MedicalHistory', [
            'patient' => $patient,
            'history' => $history,
        ]);
    }

    /**
     * View patient medical records
     */
    public function records(Request $request, Patient $patient)
    {
        $doctorId = $this->resolveDoctorId($request);
        if (!$this->canAccessPatient($request, $patient, $doctorId)) {
            abort(403, 'You are not allowed to access this patient.');
        }

        $records = MedicalRecord::where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->latest('record_date')
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
        $doctorId = $this->resolveDoctorId($request);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'symptoms' => 'nullable|array',
            'test_results' => 'nullable|array',
        ]);

        MedicalRecord::create([
            ...$validated,
            'doctor_id' => $doctorId,
            'record_date' => now(),
        ]);

        return back()->with('success', 'Medical record created successfully');
    }

    private function resolveDoctorId(Request $request): int
    {
        $user = $request->user();
        $doctorId = Doctor::query()->where('email', $user->email)->value('id');

        if ($doctorId) {
            return (int) $doctorId;
        }

        $doctorIdById = Doctor::query()->whereKey($user->id)->value('id');

        return (int) ($doctorIdById ?: $user->id);
    }

    private function canAccessPatient(Request $request, Patient $patient, int $doctorId): bool
    {
        if ((int) $patient->created_by_user_id === (int) $request->user()->id) {
            return true;
        }

        return Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->exists();
    }
}
