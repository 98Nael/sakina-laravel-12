<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor\Doctor;
use App\Models\Patient\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display patient's appointments
     */
    public function index(Request $request)
    {
        $patientId = $this->resolvePatientId($request);
        $appointments = Appointment::query()
            ->with('doctor:id,name,specialization,hospital_name')
            ->where('patient_id', $patientId)
            ->latest('date_time')
            ->get()
            ->map(function (Appointment $appointment) {
                return [
                    'id' => $appointment->id,
                    'doctor_name' => $appointment->doctor?->name ?? '-',
                    'specialty' => $appointment->doctor?->specialization ?? '-',
                    'date' => $appointment->date_time?->toDateString(),
                    'time' => $appointment->date_time?->format('h:i A'),
                    'status' => $appointment->status ?? 'scheduled',
                    'location' => $appointment->doctor?->hospital_name ?: 'Clinic',
                ];
            })
            ->values();

        return Inertia::render('Appointment/index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Book new appointment
     */
    public function create()
    {
        $doctors = Doctor::query()
            ->where('verified', true)
            ->orderBy('name')
            ->get(['id', 'name', 'specialization', 'hospital_name'])
            ->map(function (Doctor $doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialty' => $doctor->specialization,
                    'hospital_name' => $doctor->hospital_name,
                ];
            })
            ->values();

        return Inertia::render('Appointment/create', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store new appointment
     */
    public function store(Request $request)
    {
        $patientId = $this->resolvePatientId($request);

        $validated = $request->validate([
            'doctor_id' => [
                'required',
                'integer',
                Rule::exists('doctors', 'id')->where(fn ($q) => $q->where('verified', true)),
            ],
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:255',
        ]);

        $dateTime = "{$validated['date']} {$validated['time']}:00";

        Appointment::create([
            'patient_id' => $patientId,
            'doctor_id' => (int) $validated['doctor_id'],
            'date_time' => $dateTime,
            'status' => 'scheduled',
            'reason' => $validated['reason'],
            'notes' => null,
            'duration_minutes' => 30,
            'reminder_sent' => false,
        ]);
        
        return redirect()->route('patient.appointments.index')->with('success', 'Appointment booked successfully');
    }

    /**
     * Show appointment details
     */
    public function show(Request $request, $appointmentId)
    {
        $patientId = $this->resolvePatientId($request);
        $appointment = Appointment::query()
            ->with('doctor:id,name,specialization,hospital_name')
            ->where('patient_id', $patientId)
            ->findOrFail($appointmentId);

        return Inertia::render('Appointment/show', [
            'appointment' => [
                'id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'doctor_name' => $appointment->doctor?->name ?? '-',
                'specialization' => $appointment->doctor?->specialization ?? '-',
                'location' => $appointment->doctor?->hospital_name ?? 'Clinic',
                'date' => $appointment->date_time?->toDateString(),
                'time' => $appointment->date_time?->format('H:i'),
                'status' => $appointment->status,
                'reason' => $appointment->reason,
                'notes' => $appointment->notes,
            ],
        ]);
    }

    /**
     * Edit appointment
     */
    public function edit(Request $request, $appointmentId)
    {
        $patientId = $this->resolvePatientId($request);
        $appointment = Appointment::query()
            ->with('doctor:id,name,specialization')
            ->where('patient_id', $patientId)
            ->findOrFail($appointmentId);

        return Inertia::render('Appointment/edit', [
            'appointment' => [
                'id' => $appointment->id,
                'doctor_name' => $appointment->doctor?->name ?? '-',
                'specialization' => $appointment->doctor?->specialization ?? '-',
                'date' => $appointment->date_time?->toDateString(),
                'time' => $appointment->date_time?->format('H:i'),
                'reason' => $appointment->reason,
                'status' => $appointment->status,
            ],
        ]);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $appointmentId)
    {
        $patientId = $this->resolvePatientId($request);
        $appointment = Appointment::query()
            ->where('patient_id', $patientId)
            ->findOrFail($appointmentId);

        $appointment->update([
            'status' => 'cancelled',
        ]);
        
        return back()->with('success', 'Appointment cancelled');
    }

    /**
     * Reschedule appointment
     */
    public function reschedule(Request $request, $appointmentId)
    {
        $patientId = $this->resolvePatientId($request);
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        $appointment = Appointment::query()
            ->where('patient_id', $patientId)
            ->findOrFail($appointmentId);

        $appointment->update([
            'date_time' => "{$validated['date']} {$validated['time']}:00",
            'status' => 'scheduled',
        ]);
        
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

    private function resolvePatientId(Request $request): int
    {
        $user = $request->user();

        $patientId = Patient::query()
            ->where('user_id', $user->id)
            ->value('id');

        if (!$patientId) {
            $patientId = Patient::query()
                ->where('email', $user->email)
                ->value('id');
        }

        if (!$patientId) {
            abort(403, 'Patient profile not found');
        }

        return (int) $patientId;
    }
}
