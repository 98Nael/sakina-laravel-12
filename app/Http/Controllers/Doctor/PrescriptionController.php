<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrescriptionController extends Controller
{
    /**
     * Temporary in-memory dataset until DB wiring is completed.
     */
    private function demoPrescriptions(): array
    {
        return [
            [
                'id' => 1,
                'patient_id' => 101,
                'patient_name' => 'John Smith',
                'medication' => 'Lisinopril',
                'dosage' => '10mg',
                'frequency' => 'Once daily',
                'duration' => '30 days',
                'notes' => 'Take after breakfast.',
                'issued_date' => '2026-02-01',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'patient_id' => 102,
                'patient_name' => 'Emma Davis',
                'medication' => 'Metformin',
                'dosage' => '500mg',
                'frequency' => 'Twice daily',
                'duration' => '60 days',
                'notes' => 'Monitor blood sugar weekly.',
                'issued_date' => '2026-01-28',
                'status' => 'active',
            ],
        ];
    }

    /**
     * Display doctor's prescriptions
     */
    public function index(Request $request)
    {
        $doctor = $request->user();
        $prescriptions = $this->demoPrescriptions();

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
        ]);
    }

    /**
     * Create new prescription
     */
    public function create()
    {
        return Inertia::render('Prescriptions/Create');
    }

    /**
     * Store new prescription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'medication' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Save prescription to database
        
        return redirect()->route('doctor.prescriptions.index')->with('success', 'Prescription created');
    }

    /**
     * Show prescription details
     */
    public function show($prescriptionId)
    {
        $prescription = collect($this->demoPrescriptions())
            ->firstWhere('id', (int) $prescriptionId);

        if (!$prescription) {
            abort(404, 'Prescription not found');
        }

        return Inertia::render('Prescriptions/Show', [
            'prescription' => $prescription,
        ]);
    }

    /**
     * Edit prescription
     */
    public function edit($prescriptionId)
    {
        $prescription = collect($this->demoPrescriptions())
            ->firstWhere('id', (int) $prescriptionId);

        if (!$prescription) {
            abort(404, 'Prescription not found');
        }

        return Inertia::render('Prescriptions/Edit', [
            'prescription' => $prescription,
        ]);
    }

    /**
     * Update prescription
     */
    public function update(Request $request, $prescriptionId)
    {
        $validated = $request->validate([
            'medication' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Update prescription in database
        
        return redirect()->route('doctor.prescriptions.index')->with('success', 'Prescription updated');
    }

    /**
     * Cancel prescription
     */
    public function cancel(Request $request, $prescriptionId)
    {
        // Cancel prescription in database
        
        return back()->with('success', 'Prescription cancelled');
    }
}
