<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor\Doctor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display doctor's appointments
     */
    public function index(Request $request)
    {
        $doctorId = $this->resolveDoctorId($request);

        $appointments = Appointment::query()
            ->with('patient:id,name')
            ->where('doctor_id', $doctorId)
            ->latest('date_time')
            ->paginate(10)
            ->through(function (Appointment $appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient?->name ?? 'Unknown',
                    'time' => optional($appointment->date_time)->format('h:i A') ?? '-',
                    'type' => $appointment->reason ?: 'Consultation',
                    'status' => $appointment->status ?: 'scheduled',
                ];
            })
            ->withQueryString();

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show doctor's appointments calendar
     */
    public function calendar(Request $request)
    {
        $doctorId = $this->resolveDoctorId($request);
        
        $appointments = \App\Models\Appointment::where('doctor_id', $doctorId)
            ->get();

        return Inertia::render('Appointments/Calendar', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show appointment details
     */
    public function show(Request $request, $appointmentId)
    {
        $doctorId = $this->resolveDoctorId($request);
        $appointment = Appointment::query()
            ->with('patient:id,name,email,phone', 'doctor:id,name,specialization,hospital_name')
            ->where('doctor_id', $doctorId)
            ->findOrFail($appointmentId);

        return Inertia::render('Appointments/Show', [
            'appointment' => [
                'id' => $appointment->id,
                'patient_name' => $appointment->patient?->name ?? '-',
                'patient_email' => $appointment->patient?->email ?? '-',
                'patient_phone' => $appointment->patient?->phone ?? '-',
                'doctor_name' => $appointment->doctor?->name ?? '-',
                'specialization' => $appointment->doctor?->specialization ?? '-',
                'location' => $appointment->doctor?->hospital_name ?? 'Clinic',
                'date_time' => $appointment->date_time?->toDateTimeString(),
                'time' => $appointment->date_time?->format('h:i A'),
                'status' => $appointment->status,
                'reason' => $appointment->reason,
                'notes' => $appointment->notes,
            ],
        ]);
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $appointmentId)
    {
        $doctorId = $this->resolveDoctorId($request);
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no-show',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->findOrFail($appointmentId);

        $appointment->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $appointment->notes,
        ]);
        
        return back()->with('success', 'Appointment updated successfully');
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $appointmentId)
    {
        $doctorId = $this->resolveDoctorId($request);
        $appointment = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->findOrFail($appointmentId);

        $appointment->update([
            'status' => 'cancelled',
        ]);
        
        return back()->with('success', 'Appointment cancelled');
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
}
