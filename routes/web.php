<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Models\Admin\Admin;
use App\Models\AboutContent;
use App\Models\Center;
use App\Models\Doctor\Doctor;
use App\Models\PrivacyPolicy;
use App\Models\SocialLink;

// ========================================
// ADMIN CONTROLLERS
// ========================================
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportsController;

// ========================================
// DOCTOR CONTROLLERS
// ========================================
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\PrescriptionController as DoctorPrescriptionController;

// ========================================
// PATIENT CONTROLLERS
// ========================================
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\MedicalHistoryController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;
use App\Http\Controllers\Patient\Auth\RegisterController as PatientRegisterController;
use App\Http\Controllers\Patient\Auth\LoginController as PatientLoginController;
use App\Http\Controllers\Doctor\Auth\LoginController as DoctorLoginController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\ChatAssistantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

// ========================================
// PUBLIC ROUTES (بدون تسجيل دخول)
// ========================================
Route::get('/', function () {
    $doctors = Doctor::query()
        ->where('verified', true)
        ->latest()
        ->take(6)
        ->get([
            'id',
            'name',
            'specialization',
            'hospital_name',
            'bio',
            'photo_path',
        ])
        ->map(function (Doctor $doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'hospital_name' => $doctor->hospital_name,
                'bio' => $doctor->bio,
                'photo_url' => $doctor->photo_path ? asset('storage/' . $doctor->photo_path) : null,
            ];
        });

    $contactAdmin = Admin::query()->latest()->first(['email', 'phone']);
    $socialLinks = SocialLink::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['platform', 'label', 'url']);

    if ($socialLinks->isEmpty()) {
        $socialLinks = collect([
            ['platform' => 'facebook', 'label' => 'Facebook', 'url' => 'https://facebook.com/'],
            ['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/'],
            ['platform' => 'x', 'label' => 'X', 'url' => 'https://x.com/'],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://linkedin.com/'],
            ['platform' => 'youtube', 'label' => 'YouTube', 'url' => 'https://youtube.com/'],
            ['platform' => 'tiktok', 'label' => 'TikTok', 'url' => 'https://tiktok.com/'],
        ]);
    }

    $privacyPolicy = PrivacyPolicy::query()
        ->where('is_active', true)
        ->latest()
        ->first(['title', 'content']);

    if (!$privacyPolicy) {
        $privacyPolicy = (object) [
            'title' => 'Privacy Policy',
            'content' => 'We are committed to protecting your personal and medical information. Data is collected only for healthcare services, stored securely, and never shared without legal basis or your consent.',
        ];
    }

    $mapCenters = collect();

    if (Schema::hasTable('centers')
        && Schema::hasColumn('centers', 'latitude')
        && Schema::hasColumn('centers', 'longitude')
    ) {
        $mapCenters = Center::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'type',
                'city',
                'address',
                'latitude',
                'longitude',
            ])
            ->map(function (Center $center) {
                return [
                    'id' => $center->id,
                    'name' => $center->name,
                    'type' => $center->type,
                    'city' => $center->city,
                    'address' => $center->address,
                    'latitude' => (float) $center->latitude,
                    'longitude' => (float) $center->longitude,
                ];
            })
            ->values();
    }

    $googleMapsApiKey = config('services.google_maps.key')
        ?: env('VITE_GOOGLE_MAPS_API_KEY')
        ?: ($_ENV['VITE_GOOGLE_MAPS_API_KEY'] ?? null)
        ?: ($_SERVER['VITE_GOOGLE_MAPS_API_KEY'] ?? null);

    if (!$googleMapsApiKey && file_exists(base_path('.env'))) {
        $envLines = @file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($envLines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            if (!str_starts_with($trimmed, 'VITE_GOOGLE_MAPS_API_KEY=')) {
                continue;
            }

            $value = substr($trimmed, strlen('VITE_GOOGLE_MAPS_API_KEY='));
            $value = trim($value, " \t\n\r\0\x0B\"'");

            if ($value !== '') {
                $googleMapsApiKey = $value;
            }

            break;
        }
    }

    return view('welcome', [
        'doctors' => $doctors,
        'contactInfo' => [
            'phone' => $contactAdmin?->phone ?: '+966 500 000 000',
            'email' => $contactAdmin?->email ?: 'support@sakina-health.test',
            'address' => 'Sakina Health Center, Riyadh, Saudi Arabia',
            'hours' => 'Sun - Thu, 8:00 AM - 8:00 PM',
        ],
        'socialLinks' => $socialLinks,
        'privacyPolicy' => $privacyPolicy,
        'mapCenters' => $mapCenters,
        'googleMapsApiKey' => $googleMapsApiKey,
    ]);
})->name('home');

Route::get('/about', function () {
    $aboutContent = AboutContent::query()
        ->where('is_active', true)
        ->latest()
        ->first(['title', 'subtitle', 'content']);

    if (!$aboutContent) {
        $aboutContent = (object) [
            'title' => 'Serious, scalable, healthcare-focused product experience.',
            'subtitle' => 'About Platform',
            'content' => 'Sakina Health Platform is built to simplify clinical workflows and enhance visibility across healthcare operations. It unifies doctor, patient, and admin journeys into role-based digital experiences.',
        ];
    }

    return view('about', [
        'aboutContent' => $aboutContent,
    ]);
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/get-started', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect()->route('patient.register');
})->name('get-started');

Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:2000',
    ]);

    \App\Models\ContactMessage::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'subject' => $validated['subject'],
        'message' => $validated['message'],
        'status' => 'new',
    ]);

    return back()->with('success', 'Your message has been sent successfully.');
})->name('contact.send');

Route::post('/chat-assistant', [ChatAssistantController::class, 'respond'])
    ->middleware('throttle:30,1')
    ->name('chat.assistant');

// ========================================
// AUTHENTICATION ROUTES (تسجيل دخول و خروج)
// ========================================
Route::middleware('guest')->group(function () {
    // Generic login/register (redirects based on role)
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [PatientRegisterController::class, 'index'])->name('register');
    Route::post('/register', [PatientRegisterController::class, 'store']);

    // Admin-specific auth routes
    Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store']);

    // Doctor-specific auth routes
    Route::get('/doctor/login', [DoctorLoginController::class, 'index'])->name('doctor.login');
    Route::post('/doctor/login', [DoctorLoginController::class, 'store']);

    // Patient-specific auth routes
    Route::get('/patient/login', [PatientLoginController::class, 'index'])->name('patient.login');
    Route::post('/patient/login', [PatientLoginController::class, 'store']);
    Route::get('/patient/register', [PatientRegisterController::class, 'index'])->name('patient.register');
    Route::post('/patient/register', [PatientRegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->middleware('auth')->name('admin.logout');
Route::post('/doctor/logout', [DoctorLoginController::class, 'destroy'])->middleware('auth')->name('doctor.logout');
Route::post('/patient/logout', [PatientLoginController::class, 'destroy'])->middleware('auth')->name('patient.logout');

// ========================================
// AUTHENTICATED ROUTES (مشترك لجميع المستخدمين)
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->role === 'patient') {
            return redirect()->route('patient.dashboard');
        }
        abort(403, 'No dashboard for this role');
    })->name('dashboard');
});

// ========================================
// ROUTE FILES FOR EACH ROLE
// ========================================
require __DIR__ . '/admin.php';
require __DIR__ . '/doctor.php';
require __DIR__ . '/patient.php';
