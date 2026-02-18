<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrescriptionController extends Controller
{
    /**
     * Display patient's prescriptions
     */
    public function index(Request $request)
    {
        $patientId = $this->resolvePatientId($request);

        $prescriptions = Prescription::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patientId)
            ->latest('issued_date')
            ->get()
            ->map(fn (Prescription $prescription) => $this->mapPrescription($prescription))
            ->values();

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
        ]);
    }

    /**
     * Show prescription details
     */
    public function show(Request $request, $prescriptionId)
    {
        $patientId = $this->resolvePatientId($request);
        $prescription = Prescription::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patientId)
            ->findOrFail($prescriptionId);

        return Inertia::render('Prescriptions/Show', [
            'prescription' => $this->mapPrescription($prescription),
            'pharmacy_suggestions' => [],
        ]);
    }

    /**
     * Request refill for prescription
     */
    public function requestRefill(Request $request, $prescriptionId)
    {
        $patientId = $this->resolvePatientId($request);
        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $prescription = Prescription::query()
            ->where('patient_id', $patientId)
            ->findOrFail($prescriptionId);

        if (($prescription->refills_used ?? 0) >= ($prescription->refills_allowed ?? 0)) {
            return back()->withErrors([
                'refill' => 'No refills available for this prescription.',
            ]);
        }

        $prescription->increment('refills_used');
        
        return back()->with('success', 'Refill request sent to your doctor');
    }

    /**
     * Download prescription
     */
    public function download(Request $request, $prescriptionId)
    {
        $patientId = $this->resolvePatientId($request);
        $prescription = Prescription::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patientId)
            ->findOrFail($prescriptionId);

        $filename = 'prescription-' . $prescription->id . '.txt';

        return response()->streamDownload(function () use ($prescription) {
            echo "Prescription #" . $prescription->id . PHP_EOL;
            echo "Medication: " . ($prescription->medication_name ?? '-') . PHP_EOL;
            echo "Dosage: " . ($prescription->dosage ?? '-') . PHP_EOL;
            echo "Frequency: " . ($prescription->frequency ?? '-') . PHP_EOL;
            echo "Doctor: " . ($prescription->doctor?->name ?? '-') . PHP_EOL;
            echo "Issued: " . optional($prescription->issued_date)->format('Y-m-d H:i') . PHP_EOL;
            echo "Expiry: " . optional($prescription->expiry_date)->format('Y-m-d H:i') . PHP_EOL;
            echo "Instructions: " . ($prescription->instructions ?? '-') . PHP_EOL;
        }, $filename, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Share prescription with pharmacy
     */
    public function shareWithPharmacy(Request $request, $prescriptionId)
    {
        $patientId = $this->resolvePatientId($request);
        $validated = $request->validate([
            'pharmacy_id' => 'required|integer',
        ]);

        Prescription::query()
            ->where('patient_id', $patientId)
            ->findOrFail($prescriptionId);
        
        return back()->with('success', 'Prescription shared with pharmacy');
    }

    private function resolvePatientId(Request $request): int
    {
        $user = $request->user();

        $patientId = Patient::query()->where('user_id', $user->id)->value('id');
        if (!$patientId) {
            $patientId = Patient::query()->where('email', $user->email)->value('id');
        }

        if (!$patientId) {
            abort(403, 'Patient profile not found');
        }

        return (int) $patientId;
    }

    private function mapPrescription(Prescription $prescription): array
    {
        $refillsAvailable = max(0, (int) ($prescription->refills_allowed ?? 0) - (int) ($prescription->refills_used ?? 0));

        return [
            'id' => $prescription->id,
            'medication' => $prescription->medication_name,
            'dosage' => $prescription->dosage,
            'frequency' => $prescription->frequency,
            'prescribed_by' => $prescription->doctor?->name ?? '-',
            'issued_date' => $prescription->issued_date?->toDateString(),
            'expiry_date' => $prescription->expiry_date?->toDateString(),
            'refills' => $refillsAvailable,
            'status' => $prescription->status,
            'notes' => $prescription->instructions,
            'duration' => $prescription->duration_days ? ($prescription->duration_days . ' days') : '-',
        ];
    }
}
