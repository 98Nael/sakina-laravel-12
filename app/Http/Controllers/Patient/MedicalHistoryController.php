<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalHistoryController extends Controller
{
    /**
     * Display patient's medical history
     */
    public function index(Request $request)
    {
        $patientId = $this->resolvePatientId($request);

        $medicalHistory = MedicalRecord::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patientId)
            ->latest('record_date')
            ->get()
            ->map(fn (MedicalRecord $record) => $this->mapRecord($record))
            ->values();

        return Inertia::render('MedicalHistory/Index', [
            'medicalHistory' => $medicalHistory,
        ]);
    }

    /**
     * Show detailed medical record
     */
    public function show(Request $request, $recordId)
    {
        $patientId = $this->resolvePatientId($request);
        $record = MedicalRecord::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patientId)
            ->findOrFail($recordId);

        return Inertia::render('MedicalHistory/Show', [
            'record' => $this->mapRecord($record),
            'attachments' => [],
        ]);
    }

    /**
     * Download medical records
     */
    public function download(Request $request, $recordId)
    {
        $patientId = $this->resolvePatientId($request);
        $record = MedicalRecord::query()
            ->with('doctor:id,name')
            ->where('patient_id', $patientId)
            ->findOrFail($recordId);

        $filename = 'medical-record-' . $record->id . '.txt';

        return response()->streamDownload(function () use ($record) {
            echo "Medical Record #" . $record->id . PHP_EOL;
            echo "Date: " . optional($record->record_date)->format('Y-m-d H:i') . PHP_EOL;
            echo "Doctor: " . ($record->doctor?->name ?? '-') . PHP_EOL;
            echo "Diagnosis: " . ($record->diagnosis ?? '-') . PHP_EOL;
            echo "Treatment: " . ($record->treatment ?? '-') . PHP_EOL;
            echo "Notes: " . ($record->notes ?? '-') . PHP_EOL;
        }, $filename, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Request medical records from previous providers
     */
    public function requestRecords(Request $request)
    {
        $validated = $request->validate([
            'provider_name' => 'required|string',
            'provider_email' => 'required|email',
            'record_type' => 'required|in:general,lab,imaging',
        ]);

        // Send request to medical provider
        
        return back()->with('success', 'Records request sent successfully');
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

    private function mapRecord(MedicalRecord $record): array
    {
        return [
            'id' => $record->id,
            'date' => $record->record_date?->toDateString(),
            'doctor' => $record->doctor?->name ?? '-',
            'visit_type' => 'Consultation',
            'diagnosis' => $record->diagnosis,
            'notes' => $record->notes ?: $record->treatment,
            'status' => 'completed',
        ];
    }
}
