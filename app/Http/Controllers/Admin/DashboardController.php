<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function __invoke(Request $request): Response
    {
        $today = now()->toDateString();
        $hasApprovalStatus = Schema::hasColumn('patients', 'approval_status');

        $stats = [
            'totalUsers' => User::count(),
            'doctors' => User::where('role', 'doctor')->count(),
            'patients' => User::where('role', 'patient')->count(),
            'pendingPatients' => $hasApprovalStatus
                ? Patient::where('approval_status', 'pending')->count()
                : 0,
            'appointmentsToday' => Appointment::whereDate('date_time', $today)->count(),
            'completedToday' => Appointment::whereDate('date_time', $today)
                ->where('status', 'completed')
                ->count(),
        ];

        $system = [
            'database' => $this->databaseStatus(),
            'api' => 'Operational',
            'queue' => 'Idle',
        ];

        return Inertia::render('Dashboard', [
            'user' => $request->user(),
            'appName' => config('app.name'),
            'stats' => $stats,
            'system' => $system,
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
}
