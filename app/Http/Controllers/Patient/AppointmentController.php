<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display patient's appointments
     */
    public function index(Request $request)
    {
        $patient = $request->user();
        
        $appointments = [
            [
                'id' => 1,
                'doctor_name' => 'Dr. Sarah Johnson',
                'specialty' => 'Cardiologist',
                'date' => '2026-02-14',
                'time' => '10:00 AM',
                'status' => 'confirmed',
                'location' => 'Room 305',
            ],
            [
                'id' => 2,
                'doctor_name' => 'Dr. Michael Chen',
                'specialty' => 'General Physician',
                'date' => '2026-02-21',
                'time' => '02:30 PM',
                'status' => 'scheduled',
                'location' => 'Room 102',
            ],
        ];

        return Inertia::render('Appointment/index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Book new appointment
     */
    public function create()
    {
        $doctors = [
            ['id' => 1, 'name' => 'Dr. Sarah Johnson', 'specialty' => 'Cardiologist'],
            ['id' => 2, 'name' => 'Dr. Michael Chen', 'specialty' => 'General Physician'],
            ['id' => 3, 'name' => 'Dr. Lisa Anderson', 'specialty' => 'Dermatologist'],
        ];

        return Inertia::render('Appointment/create', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store new appointment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after:today',
            'time' => 'required',
            'reason' => 'required|string|max:255',
        ]);

        // Save appointment to database
        
        return redirect()->route('patient.appointments.index')->with('success', 'Appointment booked successfully');
    }

    /**
     * Show appointment details
     */
    public function show($appointmentId)
    {
        return Inertia::render('Patient/Appointments/Show', [
            'appointment' => [], // Add real data
        ]);
    }

    /**
     * Edit appointment
     */
    public function edit($appointmentId)
    {
        return Inertia::render('Patient/Appointments/Edit', [
            'appointment' => [], // Add real data
        ]);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $appointmentId)
    {
        // Cancel appointment in database
        
        return back()->with('success', 'Appointment cancelled');
    }

    /**
     * Reschedule appointment
     */
    public function reschedule(Request $request, $appointmentId)
    {
        $validated = $request->validate([
            'date' => 'required|date|after:today',
            'time' => 'required',
        ]);

        // Update appointment in database
        
        return back()->with('success', 'Appointment rescheduled successfully');
    }

    /**
     * View available doctors
     */
    public function doctors()
    {
        $doctors = \App\Models\Doctor\Doctor::select('id', 'name', 'specialization', 'hospital_name', 'rating')
            ->where('verified', true)
            ->paginate(15);

        return Inertia::render('Patient/Appointments/Doctors', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * View doctor profile
     */
    public function doctorProfile(\App\Models\Doctor\Doctor $doctor)
    {
        return Inertia::render('Patient/Appointments/DoctorProfile', [
            'doctor' => $doctor,
        ]);
    }
}
