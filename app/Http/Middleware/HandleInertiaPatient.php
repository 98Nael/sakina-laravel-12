<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaPatient extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'Patient/app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $patient = $user && $user->role === 'patient' ? $user : null;

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $patient ? [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'role' => $patient->role,
                ] : null,
            ],
            'user' => [
                'name' => $patient?->name ?? '',
                'email' => $patient?->email ?? '',
                'locale' => App::getLocale(),
                'fallback_locale' => config('app.fallback_locale'),
                'permission' => Session::get('permission'),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('user_message'),
                'additional_data' => fn () => $request->session()->get('additional_data'),
                'locales' => config('translatable.locales')
            ],
        ]);
    }
}
