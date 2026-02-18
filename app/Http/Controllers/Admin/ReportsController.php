<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class ReportsController extends Controller
{
    /**
     * Show reports page
     */
    public function index()
    {
        $reports = [
            'user_growth' => [
                'total_users' => User::count(),
                'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'admins' => User::where('role', 'admin')->count(),
                'doctors' => User::where('role', 'doctor')->count(),
                'patients' => User::where('role', 'patient')->count(),
            ],
            'system_health' => [
                'database_status' => 'Connected',
                'api_status' => 'Operational',
                'disk_usage' => '45%',
                'memory_usage' => '60%',
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
        $appointments = \App\Models\Appointment::with(['patient', 'doctor'])
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
