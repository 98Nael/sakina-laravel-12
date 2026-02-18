<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the patient dashboard
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
              'user' => $request->user(),
            'appName' => config('app.name'),
        ]);
    }
}
