# Complete Routes Setup Examples

This document shows how to register all the newly created controllers in your routes/web.php file.

## 📋 Full Routes Setup

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportsController;

// Doctor Controllers
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\PrescriptionController as DoctorPrescriptionController;

// Patient Controllers
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\MedicalHistoryController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// AUTHENTICATION ROUTES
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ==========================================
// GENERAL AUTHENTICATED ROUTES
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    // User Management
    Route::resource('users', UserController::class)->names('users');
    
    // System Settings
    Route::resource('settings', SettingsController::class)->names('settings');
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/users', [ReportsController::class, 'userReport'])->name('reports.users');
    Route::get('/reports/system', [ReportsController::class, 'systemReport'])->name('reports.system');
    Route::post('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
});

// ==========================================
// DOCTOR ROUTES
// ==========================================
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Doctor Dashboard
    Route::get('/dashboard', DoctorDashboardController::class)->name('dashboard');

    // Patient Management
    Route::resource('patients', PatientController::class)->names('patients');
    Route::post('/patients/{patient}/notes', [PatientController::class, 'addNote'])->name('patients.addNote');
    Route::get('/patients/{patient}/history', [PatientController::class, 'medicalHistory'])->name('patients.medicalHistory');

    // Appointments
    Route::resource('appointments', DoctorAppointmentController::class)->names('appointments');
    Route::put('/appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::delete('/appointments/{appointment}', [DoctorAppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Prescriptions
    Route::resource('prescriptions', DoctorPrescriptionController::class)->names('prescriptions');
    Route::delete('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'cancel'])->name('prescriptions.cancel');
});

// ==========================================
// PATIENT ROUTES
// ==========================================
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    // Patient Dashboard
    Route::get('/dashboard', PatientDashboardController::class)->name('dashboard');

    // Appointments
    Route::resource('appointments', PatientAppointmentController::class)->names('appointments');
    Route::delete('/appointments/{appointment}', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::put('/appointments/{appointment}', [PatientAppointmentController::class, 'reschedule'])->name('appointments.reschedule');

    // Medical History
    Route::resource('medical-history', MedicalHistoryController::class)->names('medical_history');
    Route::get('/medical-history/{record}/download', [MedicalHistoryController::class, 'download'])->name('medical_history.download');
    Route::post('/medical-history/request', [MedicalHistoryController::class, 'requestRecords'])->name('medical_history.request');

    // Prescriptions
    Route::resource('prescriptions', PatientPrescriptionController::class)->names('prescriptions');
    Route::post('/prescriptions/{prescription}/refill', [PatientPrescriptionController::class, 'requestRefill'])->name('prescriptions.refill');
    Route::get('/prescriptions/{prescription}/download', [PatientPrescriptionController::class, 'download'])->name('prescriptions.download');
    Route::post('/prescriptions/{prescription}/share-pharmacy', [PatientPrescriptionController::class, 'shareWithPharmacy'])->name('prescriptions.sharePharmacy');
});
```

## 📝 Route Naming Convention

### Admin Routes
```
admin.dashboard                    - Dashboard
admin.users.index                  - List users
admin.users.create                 - Create user form
admin.users.store                  - Store user
admin.users.show                   - View user
admin.users.edit                   - Edit form
admin.users.update                 - Update user
admin.users.destroy                - Delete user
admin.settings.index               - Settings
admin.reports.index                - Reports
admin.reports.users                - User report
admin.reports.system               - System report
admin.reports.export               - Export report
```

### Doctor Routes
```
doctor.dashboard                   - Dashboard
doctor.patients.index              - Patient list
doctor.patients.create             - Add patient
doctor.patients.store              - Store patient
doctor.patients.show               - Patient details
doctor.patients.edit               - Edit patient
doctor.patients.update             - Update patient
doctor.patients.destroy            - Delete patient
doctor.patients.addNote            - Add clinical note
doctor.patients.medicalHistory     - Patient history
doctor.appointments.index          - My appointments
doctor.appointments.show           - Appointment details
doctor.appointments.updateStatus   - Update status
doctor.appointments.cancel         - Cancel appointment
doctor.prescriptions.index         - Prescriptions
doctor.prescriptions.create        - Write prescription
doctor.prescriptions.store         - Save prescription
doctor.prescriptions.show          - View prescription
doctor.prescriptions.edit          - Edit prescription
doctor.prescriptions.cancel        - Cancel prescription
```

### Patient Routes
```
patient.dashboard                  - Dashboard
patient.appointments.index         - My appointments
patient.appointments.create        - Book appointment
patient.appointments.store         - Save appointment
patient.appointments.show          - View appointment
patient.appointments.cancel        - Cancel appointment
patient.appointments.reschedule    - Reschedule appointment
patient.medical_history.index      - Medical history
patient.medical_history.show       - Record details
patient.medical_history.download   - Download records
patient.medical_history.request    - Request records
patient.prescriptions.index        - My prescriptions
patient.prescriptions.show         - Prescription details
patient.prescriptions.refill       - Request refill
patient.prescriptions.download     - Download prescription
patient.prescriptions.sharePharmacy - Share with pharmacy
```

## 🔗 URL Structure

### Admin URLs
```
/admin/dashboard                   GET
/admin/users                       GET, POST
/admin/users/create                GET
/admin/users/{id}                  GET, PUT, DELETE
/admin/users/{id}/edit             GET
/admin/settings                    GET, POST
/admin/reports                     GET
/admin/reports/users               GET
/admin/reports/system              GET
/admin/reports/export              POST
```

### Doctor URLs
```
/doctor/dashboard                  GET
/doctor/patients                   GET, POST
/doctor/patients/create            GET
/doctor/patients/{id}              GET, PUT, DELETE
/doctor/patients/{id}/edit         GET
/doctor/patients/{id}/notes        POST
/doctor/patients/{id}/history      GET
/doctor/appointments               GET, POST
/doctor/appointments/{id}          GET, PUT, DELETE
/doctor/appointments/{id}/status   PUT
/doctor/prescriptions              GET, POST
/doctor/prescriptions/create       GET
/doctor/prescriptions/{id}         GET, PUT, DELETE
/doctor/prescriptions/{id}/edit    GET
```

### Patient URLs
```
/patient/dashboard                 GET
/patient/appointments              GET, POST
/patient/appointments/create       GET
/patient/appointments/{id}         GET, PUT, DELETE
/patient/medical-history           GET
/patient/medical-history/{id}      GET
/patient/medical-history/{id}/download GET
/patient/medical-history/request   POST
/patient/prescriptions             GET
/patient/prescriptions/{id}        GET
/patient/prescriptions/{id}/refill POST
/patient/prescriptions/{id}/download GET
/patient/prescriptions/{id}/share-pharmacy POST
```

## 🧪 Testing Routes

### Using php artisan route:list
```bash
php artisan route:list
```

### Check specific route
```bash
php artisan route:list | grep admin
php artisan route:list | grep doctor
php artisan route:list | grep patient
```

## 🛡️ Middleware Stack

All role-based routes use:
- `auth` - User must be authenticated
- `role:admin|doctor|patient` - User must have specific role

Example:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Only authenticated admin users can access
});
```

## 💡 Usage in Blade Templates

### Generate route URLs
```blade
<!-- Admin -->
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<a href="{{ route('admin.users.index') }}">Users</a>
<a href="{{ route('admin.users.create') }}">Add User</a>

<!-- Doctor -->
<a href="{{ route('doctor.dashboard') }}">Dashboard</a>
<a href="{{ route('doctor.patients.index') }}">Patients</a>
<a href="{{ route('doctor.appointments.index') }}">Appointments</a>

<!-- Patient -->
<a href="{{ route('patient.dashboard') }}">Dashboard</a>
<a href="{{ route('patient.appointments.index') }}">My Appointments</a>
<a href="{{ route('patient.prescriptions.index') }}">My Prescriptions</a>
```

## 🎯 Controllers vs Routes Summary

| Module | Controller | Primary Route | Methods |
|--------|-----------|---------------|---------|
| **Admin** | | | |
| | UserController | /admin/users | 7 (CRUD) |
| | SettingsController | /admin/settings | 2 |
| | ReportsController | /admin/reports | 4 |
| **Doctor** | | | |
| | PatientController | /doctor/patients | 4 |
| | AppointmentController | /doctor/appointments | 4 |
| | PrescriptionController | /doctor/prescriptions | 6 |
| **Patient** | | | |
| | AppointmentController | /patient/appointments | 5 |
| | MedicalHistoryController | /patient/medical-history | 4 |
| | PrescriptionController | /patient/prescriptions | 5 |

---

**Copy the full route setup above into your routes/web.php file to enable all controllers!**
