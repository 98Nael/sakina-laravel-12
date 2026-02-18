<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalHistoryController extends Controller
{
    /**
     * Display patient's medical history
     */
    public function index(Request $request)
    {
        $patient = $request->user();
        
        $medicalHistory = [
            [
                'id' => 1,
                'date' => '2026-01-28',
                'doctor' => 'Dr. Sarah Johnson',
                'visit_type' => 'Check-up',
                'diagnosis' => 'Hypertension',
                'notes' => 'Blood pressure elevated, prescribed Lisinopril',
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'date' => '2025-12-15',
                'doctor' => 'Dr. Michael Chen',
                'visit_type' => 'Consultation',
                'diagnosis' => 'Type 2 Diabetes',
                'notes' => 'Started on Metformin 500mg twice daily',
                'status' => 'completed',
            ],
        ];

        return Inertia::render('MedicalHistory/Index', [
            'medicalHistory' => $medicalHistory,
        ]);
    }

    /**
     * Show detailed medical record
     */
    public function show($recordId)
    {
        return Inertia::render('MedicalHistory/Show', [
            'record' => [], // Add real data
            'attachments' => [], // Add real data
        ]);
    }

    /**
     * Download medical records
     */
    public function download($recordId)
    {
        // Generate and download medical record file
        return response()->download('path/to/file');
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
}
