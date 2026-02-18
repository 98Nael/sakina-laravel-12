<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display doctor's appointments
     */
    public function index(Request $request)
    {
        $doctor = $request->user();
        
        $appointments = [
            [
                'id' => 1,
                'patient_name' => 'John Smith',
                'time' => '09:00 AM',
                'type' => 'Consultation',
                'status' => 'scheduled',
            ],
            [
                'id' => 2,
                'patient_name' => 'Emma Davis',
                'time' => '10:30 AM',
                'type' => 'Check-up',
                'status' => 'scheduled',
            ],
            [
                'id' => 3,
                'patient_name' => 'Michael Brown',
                'time' => '02:00 PM',
                'type' => 'Follow-up',
                'status' => 'scheduled',
            ],
        ];

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show doctor's appointments calendar
     */
    public function calendar(Request $request)
    {
        $doctor = $request->user();
        
        $appointments = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->get();

        return Inertia::render('Appointments/Calendar', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show appointment details
     */
    public function show($appointmentId)
    {
        return Inertia::render('Appointments/Show', [
            'appointment' => [], // Add real data
        ]);
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $appointmentId)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no-show',
            'notes' => 'nullable|string',
        ]);

        // Update appointment in database
        
        return back()->with('success', 'Appointment updated successfully');
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $appointmentId)
    {
        // Cancel appointment
        
        return back()->with('success', 'Appointment cancelled');
    }
}
