<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrescriptionController extends Controller
{
    /**
     * Display patient's prescriptions
     */
    public function index(Request $request)
    {
        $patient = $request->user();
        
        $prescriptions = [
            [
                'id' => 1,
                'medication' => 'Lisinopril',
                'dosage' => '10mg',
                'frequency' => 'Once daily',
                'prescribed_by' => 'Dr. Sarah Johnson',
                'issued_date' => '2026-02-01',
                'expiry_date' => '2026-05-01',
                'refills' => 2,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'medication' => 'Metformin',
                'dosage' => '500mg',
                'frequency' => 'Twice daily',
                'prescribed_by' => 'Dr. Michael Chen',
                'issued_date' => '2026-01-28',
                'expiry_date' => '2026-04-28',
                'refills' => 3,
                'status' => 'active',
            ],
            [
                'id' => 3,
                'medication' => 'Aspirin',
                'dosage' => '81mg',
                'frequency' => 'Once daily',
                'prescribed_by' => 'Dr. Sarah Johnson',
                'issued_date' => '2025-11-15',
                'expiry_date' => '2026-02-15',
                'refills' => 0,
                'status' => 'expired',
            ],
        ];

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
        ]);
    }

    /**
     * Show prescription details
     */
    public function show($prescriptionId)
    {
        return Inertia::render('Prescriptions/Show', [
            'prescription' => [], // Add real data
            'pharmacy_suggestions' => [], // Add real data
        ]);
    }

    /**
     * Request refill for prescription
     */
    public function requestRefill(Request $request, $prescriptionId)
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        // Send refill request to doctor
        
        return back()->with('success', 'Refill request sent to your doctor');
    }

    /**
     * Download prescription
     */
    public function download($prescriptionId)
    {
        // Generate and download prescription file
        return response()->download('path/to/file');
    }

    /**
     * Share prescription with pharmacy
     */
    public function shareWithPharmacy(Request $request, $prescriptionId)
    {
        $validated = $request->validate([
            'pharmacy_id' => 'required|integer',
        ]);

        // Share prescription with selected pharmacy
        
        return back()->with('success', 'Prescription shared with pharmacy');
    }
}
