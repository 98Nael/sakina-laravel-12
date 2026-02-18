<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor\Doctor;
use App\Models\Patient\Patient;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PrescriptionController extends Controller
{
    /**
     * Display doctor's prescriptions
     */
    public function index(Request $request)
    {
        $doctorId = $this->resolveDoctorId($request);

        $prescriptions = Prescription::query()
            ->with('patient:id,name')
            ->where('doctor_id', $doctorId)
            ->latest('issued_date')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Prescription $prescription) => $this->mapPrescription($prescription));

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
        ]);
    }

    /**
     * Create new prescription
     */
    public function create(Request $request)
    {
        $patients = $this->doctorPatientsQuery($request)
            ->orderBy('name')
            ->get(['patients.id', 'patients.name', 'patients.email', 'patients.approval_status']);

        return Inertia::render('Prescriptions/Create', [
            'patients' => $patients,
        ]);
    }

    /**
     * Store new prescription
     */
    public function store(Request $request)
    {
        $doctorId = $this->resolveDoctorId($request);
        $allowedPatientIds = $this->doctorPatientsQuery($request)->pluck('patients.id')->all();

        $validated = $request->validate([
            'patient_id' => [
                'required',
                'integer',
                Rule::in($allowedPatientIds),
            ],
            'medication' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Prescription::create([
            'patient_id' => (int) $validated['patient_id'],
            'doctor_id' => $doctorId,
            'medication_name' => $validated['medication'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'duration_days' => $this->extractDurationDays($validated['duration']),
            'instructions' => $validated['notes'] ?? null,
            'status' => 'active',
            'issued_date' => now(),
        ]);
        
        return redirect()->route('doctor.prescriptions.index')->with('success', 'Prescription created');
    }

    /**
     * Show prescription details
     */
    public function show(Request $request, $prescriptionId)
    {
        $doctorId = $this->resolveDoctorId($request);
        $prescription = Prescription::query()
            ->with('patient:id,name')
            ->where('doctor_id', $doctorId)
            ->find($prescriptionId);

        if (!$prescription) {
            abort(404, 'Prescription not found');
        }

        return Inertia::render('Prescriptions/Show', [
            'prescription' => $this->mapPrescription($prescription),
        ]);
    }

    /**
     * Edit prescription
     */
    public function edit(Request $request, $prescriptionId)
    {
        $doctorId = $this->resolveDoctorId($request);
        $prescription = Prescription::query()
            ->with('patient:id,name')
            ->where('doctor_id', $doctorId)
            ->find($prescriptionId);

        if (!$prescription) {
            abort(404, 'Prescription not found');
        }

        return Inertia::render('Prescriptions/Edit', [
            'prescription' => $this->mapPrescription($prescription),
        ]);
    }

    /**
     * Update prescription
     */
    public function update(Request $request, $prescriptionId)
    {
        $doctorId = $this->resolveDoctorId($request);

        $validated = $request->validate([
            'medication' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $prescription = Prescription::query()
            ->where('doctor_id', $doctorId)
            ->findOrFail($prescriptionId);

        $prescription->update([
            'medication_name' => $validated['medication'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'duration_days' => $this->extractDurationDays($validated['duration']),
            'instructions' => $validated['notes'] ?? null,
        ]);
        
        return redirect()->route('doctor.prescriptions.index')->with('success', 'Prescription updated');
    }

    /**
     * Cancel prescription
     */
    public function cancel(Request $request, $prescriptionId)
    {
        $doctorId = $this->resolveDoctorId($request);

        $prescription = Prescription::query()
            ->where('doctor_id', $doctorId)
            ->findOrFail($prescriptionId);

        $prescription->update([
            'status' => 'cancelled',
        ]);
        
        return back()->with('success', 'Prescription cancelled');
    }

    private function resolveDoctorId(Request $request): int
    {
        $user = $request->user();
        $doctorId = Doctor::query()->where('email', $user->email)->value('id');

        if ($doctorId) {
            return (int) $doctorId;
        }

        $doctorIdById = Doctor::query()->whereKey($user->id)->value('id');

        // Fallback to user id to avoid blocking doctor pages when doctor profile row is missing.
        return (int) ($doctorIdById ?: $user->id);
    }

    private function extractDurationDays(string $rawDuration): int
    {
        preg_match('/\d+/', $rawDuration, $matches);

        return max(1, (int) ($matches[0] ?? 1));
    }

    private function mapPrescription(Prescription $prescription): array
    {
        return [
            'id' => $prescription->id,
            'patient_id' => $prescription->patient_id,
            'patient_name' => $prescription->patient?->name ?? '-',
            'medication' => $prescription->medication_name,
            'dosage' => $prescription->dosage,
            'frequency' => $prescription->frequency,
            'duration' => $prescription->duration_days . ' days',
            'notes' => $prescription->instructions,
            'issued_date' => $prescription->issued_date?->toDateString(),
            'status' => $prescription->status,
        ];
    }

    private function doctorPatientsQuery(Request $request): Builder
    {
        $doctorUserId = $request->user()->id;
        $doctorId = $this->resolveDoctorId($request);

        return Patient::query()
            ->where('approval_status', 'approved')
            ->where(function (Builder $query) use ($doctorUserId, $doctorId) {
                $query->where('created_by_user_id', $doctorUserId)
                    ->orWhereExists(function ($subQuery) use ($doctorId) {
                        $subQuery->selectRaw('1')
                            ->from('appointments')
                            ->whereColumn('appointments.patient_id', 'patients.id')
                            ->where('appointments.doctor_id', $doctorId);
                    })
                    ->orWhereExists(function ($subQuery) use ($doctorId) {
                        $subQuery->selectRaw('1')
                            ->from('patient_doctors')
                            ->whereColumn('patient_doctors.patient_id', 'patients.id')
                            ->where('patient_doctors.doctor_id', $doctorId);
                    });
            });
    }
}
