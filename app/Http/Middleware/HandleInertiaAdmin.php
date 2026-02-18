<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaAdmin extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'Admin/app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $admin = $user && $user->role === 'admin' ? $user : null;

        return array_merge(parent::share($request), [
            'user' => [
                'name' => $admin?->name ?? '',
                'email' => $admin?->email ?? '',
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
