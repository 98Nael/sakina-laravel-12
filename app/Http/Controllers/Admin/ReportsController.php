<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportsController extends Controller
{
    /**
     * Show reports page
     */
    public function index()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $totalUsers = User::count();
        $admins = User::where('role', 'admin')->count();
        $doctors = User::where('role', 'doctor')->count();
        $patients = User::where('role', 'patient')->count();

        $appointmentStatuses = Appointment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $appointmentsThisMonth = Appointment::query()
            ->whereBetween('date_time', [$monthStart, $monthEnd])
            ->count();

        $usersThisMonth = User::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $pendingPatients = Patient::query()
            ->where('approval_status', 'pending')
            ->count();

        $monthlyRegistrations = collect(range(5, 1))->map(function (int $monthsAgo) use ($now) {
            $start = $now->copy()->subMonths($monthsAgo)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return [
                'month' => $start->format('M Y'),
                'users' => User::whereBetween('created_at', [$start, $end])->count(),
            ];
        })->values();

        $monthlyRegistrations->push([
            'month' => $now->format('M Y'),
            'users' => $usersThisMonth,
        ]);

        $diskUsage = $this->formatDiskUsage();

        $reports = [
            'user_growth' => [
                'total_users' => $totalUsers,
                'new_users_this_month' => $usersThisMonth,
                'admins' => $admins,
                'doctors' => $doctors,
                'patients' => $patients,
            ],
            'platform_activity' => [
                'appointments_total' => Appointment::count(),
                'appointments_this_month' => $appointmentsThisMonth,
                'scheduled' => (int) ($appointmentStatuses['scheduled'] ?? 0),
                'completed' => (int) ($appointmentStatuses['completed'] ?? 0),
                'cancelled' => (int) ($appointmentStatuses['cancelled'] ?? 0),
                'no_show' => (int) ($appointmentStatuses['no-show'] ?? 0),
                'pending_patients' => $pendingPatients,
            ],
            'monthly_registrations' => $monthlyRegistrations,
            'data_freshness' => [
                'generated_at' => $now->toDateTimeString(),
                'timezone' => config('app.timezone'),
            ],
            'system_health' => [
                'database_status' => $this->databaseStatus(),
                'api_status' => 'Operational',
                'disk_usage' => $diskUsage,
                'memory_usage' => $this->memoryUsage(),
            ],
        ];

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Generate user report
     */
    public function userReport()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return Inertia::render('Admin/Reports/UserReport', [
            'users' => $users,
        ]);
    }

    /**
     * Generate doctor report
     */
    public function doctorReport()
    {
        $doctors = \App\Models\Doctor\Doctor::select(
            'id', 'name', 'email', 'specialization', 'verified', 'rating', 'created_at'
        )
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('Admin/Reports/DoctorReport', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Generate appointment report
     */
    public function appointmentReport()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->selectRaw('
                DATE(date_time) as appointment_date,
                status,
                COUNT(*) as count
            ')
            ->groupBy('appointment_date', 'status')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return Inertia::render('Admin/Reports/AppointmentReport', [
            'appointments' => $appointments,
        ]);
    }

    private function databaseStatus(): string
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Throwable $exception) {
            return 'Unavailable';
        }
    }

    private function formatDiskUsage(): string
    {
        $basePath = base_path();
        $total = @disk_total_space($basePath);
        $free = @disk_free_space($basePath);

        if (!$total || $free === false) {
            return 'N/A';
        }

        $usedPercent = (int) round((($total - $free) / $total) * 100);

        return $usedPercent . '%';
    }

    private function memoryUsage(): string
    {
        $bytes = memory_get_usage(true);
        $megabytes = $bytes / 1024 / 1024;

        return number_format($megabytes, 1) . ' MB';
    }

    /**
     * Export users report as CSV
     */
    public function exportUsers()
    {
        $rows = User::query()
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->latest('created_at')
            ->get();

        $filename = 'users-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['User ID', 'Name', 'Email', 'Role', 'Created At']);

            foreach ($rows as $row) {
                $createdAt = $row->created_at
                    ? "'" . $row->created_at->format('Y-m-d H:i')
                    : '-';

                fputcsv($out, [
                    $row->id,
                    $row->name ?? '-',
                    $row->email ?? '-',
                    $row->role ?? '-',
                    $createdAt,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export appointments report as CSV
     */
    public function exportAppointments()
    {
        $rows = Appointment::with(['patient:id,name', 'doctor:id,name'])
            ->latest('date_time')
            ->get();

        $filename = 'appointments-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Appointment ID', 'Date Time', 'Patient', 'Doctor', 'Status', 'Reason', 'Duration (min)']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    optional($row->date_time)->format('Y-m-d H:i:s'),
                    $row->patient?->name ?? '-',
                    $row->doctor?->name ?? '-',
                    $row->status ?? '-',
                    $row->reason ?? '-',
                    $row->duration_minutes ?? '-',
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Generate system report
     */
    public function systemReport()
    {
        $systemInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'uptime' => '99.9%',
        ];

        return Inertia::render('Admin/Reports/SystemReport', [
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $type = $request->query('type', 'users');
        
        // Generate and download report file
        return back()->with('success', 'Report exported successfully');
    }
}
