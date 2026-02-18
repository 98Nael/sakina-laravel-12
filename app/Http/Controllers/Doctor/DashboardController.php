<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor\Doctor;
use App\Models\Patient\Patient;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the doctor dashboard
     */
    public function __invoke(Request $request): Response
    {
        $doctorId = $this->resolveDoctorId($request);
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $totalPatients = Patient::query()
            ->where('approval_status', 'approved')
            ->where(function (Builder $query) use ($request, $doctorId) {
                $query->where('created_by_user_id', $request->user()->id)
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
            })
            ->distinct()
            ->count('patients.id');

        $todayAppointments = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereBetween('date_time', [$todayStart, $todayEnd])
            ->count();

        $activePrescriptions = Prescription::query()
            ->where('doctor_id', $doctorId)
            ->where('status', 'active')
            ->count();

        $consultationMinutes = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->whereBetween('date_time', [$weekStart, $weekEnd])
            ->sum('duration_minutes');

        $todaySchedule = Appointment::query()
            ->with('patient:id,name')
            ->where('doctor_id', $doctorId)
            ->whereBetween('date_time', [$todayStart, $todayEnd])
            ->orderBy('date_time')
            ->limit(10)
            ->get()
            ->map(function (Appointment $appointment): array {
                return [
                    'id' => $appointment->id,
                    'patient' => $appointment->patient?->name ?? 'Unknown patient',
                    'time' => optional($appointment->date_time)->format('h:i A') ?? '-',
                    'type' => $appointment->reason ?: 'Consultation',
                    'status' => $appointment->status,
                    'status_label' => str($appointment->status)->replace('-', ' ')->title()->toString(),
                ];
            })
            ->values();

        return Inertia::render('Dashboard', [
            'user' => $request->user(),
            'appName' => config('app.name'),
            'stats' => [
                'totalPatients' => $totalPatients,
                'todayAppointments' => $todayAppointments,
                'activePrescriptions' => $activePrescriptions,
                'consultationHours' => round($consultationMinutes / 60, 1),
            ],
            'todaySchedule' => $todaySchedule,
        ]);
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
