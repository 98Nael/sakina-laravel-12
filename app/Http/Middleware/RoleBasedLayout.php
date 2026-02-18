<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedLayout extends Middleware
{
    /**
     * Skip this middleware for patient routes because they already use
     * a dedicated Inertia middleware (HandleInertiaPatient).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('patient.*')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    /**
     * Determines the root template loaded on the first Inertia visit.
     */
    public function rootView(Request $request): string
    {
        if (!auth()->check()) {
            return 'app';
        }

        return match (auth()->user()->role) {
            'admin' => 'Admin/app',
            'doctor' => 'Doctor/app',
            'patient' => 'Patient/app',
            default => 'app',
        };
    }

    /**
     * Share data with Inertia on every request.
     */
    public function share(Request $request): array
    {
        $shared = parent::share($request);

        $user = $request->user();

        $shared['auth'] = [
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ] : null,
        ];

        return $shared;
    }
}
