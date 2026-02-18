<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
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
        return Inertia::render('Dashboard', [
            'user' => $request->user(),
            'appName' => config('app.name'),
        ]);
    }
}
