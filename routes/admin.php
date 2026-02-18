<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ContactMessageController;

// ========================================
// 👨‍💼 ADMIN ROUTES (بريفكس: /admin)
// ========================================
Route::middleware(['auth', 'role:admin', 'inertia_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // لوحة التحكم
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact_messages.index');
    Route::patch('/contact-messages/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('contact_messages.updateStatus');
    Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact_messages.destroy');
    
    // إدارة المستخدمين
    Route::patch('/users/{user}/status', [UserController::class, 'updateUserStatus'])->name('users.updateStatus');
    Route::resource('users', UserController::class)->names('users');
    
    // إدارة المشرفين
    Route::get('/admins', [UserController::class, 'admins'])->name('admins.index');
    Route::post('/admins', [UserController::class, 'storeAdmin'])->name('admins.store');
    Route::put('/admins/{admin}', [UserController::class, 'updateAdmin'])->name('admins.update');
    Route::delete('/admins/{admin}', [UserController::class, 'destroyAdmin'])->name('admins.destroy');
    
    // إدارة الأطباء
    Route::get('/doctors', [UserController::class, 'doctors'])->name('doctors.index');
    Route::get('/doctors/create', [UserController::class, 'createDoctor'])->name('doctors.create');
    Route::post('/doctors', [UserController::class, 'storeDoctor'])->name('doctors.store');
    Route::get('/doctors/{doctor}', [UserController::class, 'showDoctor'])->name('doctors.show');
    Route::get('/doctors/{doctor}/export-pdf', [UserController::class, 'exportDoctorPdf'])->name('doctors.exportPdf');
    Route::patch('/doctors/{doctor}/status', [UserController::class, 'updateDoctorStatus'])->name('doctors.updateStatus');
    Route::put('/doctors/{doctor}', [UserController::class, 'updateDoctor'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [UserController::class, 'destroyDoctor'])->name('doctors.destroy');
    
    // إدارة المرضى
    Route::get('/patients', [UserController::class, 'patients'])->name('patients.index');
    Route::post('/patients', [UserController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patient}/export-pdf', [UserController::class, 'exportPatientPdf'])->name('patients.exportPdf');
    Route::get('/patients/pending', [UserController::class, 'pendingPatients'])->name('patients.pending');
    Route::patch('/patients/{patient}', [UserController::class, 'updatePatient'])->name('patients.update');
    Route::patch('/patients/{patient}/approve', [UserController::class, 'approvePatient'])->name('patients.approve');
    Route::patch('/patients/{patient}/reject', [UserController::class, 'rejectPatient'])->name('patients.reject');
    Route::delete('/patients/{patient}', [UserController::class, 'destroyPatient'])->name('patients.destroy');
    
    // الإعدادات
    Route::resource('settings', SettingsController::class)->names('settings')->only(['index', 'store']);
    Route::patch('/settings/social-links/{socialLink}/status', [SettingsController::class, 'updateSocialLinkStatus'])->name('settings.social.updateStatus');
    Route::delete('/settings/social-links/{socialLink}', [SettingsController::class, 'destroySocialLink'])->name('settings.social.destroy');
    Route::post('/settings/privacy-policies', [SettingsController::class, 'storePrivacyPolicy'])->name('settings.privacy.store');
    Route::put('/settings/privacy-policies/{privacyPolicy}', [SettingsController::class, 'updatePrivacyPolicy'])->name('settings.privacy.update');
    Route::delete('/settings/privacy-policies/{privacyPolicy}', [SettingsController::class, 'destroyPrivacyPolicy'])->name('settings.privacy.destroy');
    Route::post('/settings/about-contents', [SettingsController::class, 'storeAboutContent'])->name('settings.about.store');
    Route::put('/settings/about-contents/{aboutContent}', [SettingsController::class, 'updateAboutContent'])->name('settings.about.update');
    Route::delete('/settings/about-contents/{aboutContent}', [SettingsController::class, 'destroyAboutContent'])->name('settings.about.destroy');
    
    // التقارير والتحليلات
    Route::get('/Appointment Report', function () {
        return redirect()->route('admin.reports.appointments');
    });
    Route::get('/User Report', function () {
        return redirect()->route('admin.reports.users');
    });
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/users', [ReportsController::class, 'userReport'])->name('reports.users');
    Route::get('/reports/users/Export', [ReportsController::class, 'exportUsers'])->name('reports.users.export');
    Route::get('/reports/users/export', [ReportsController::class, 'exportUsers']);
    Route::get('/reports/doctors', [ReportsController::class, 'doctorReport'])->name('reports.doctors');
    Route::get('/reports/appointments', [ReportsController::class, 'appointmentReport'])->name('reports.appointments');
    Route::get('/reports/appointments/Export', [ReportsController::class, 'exportAppointments'])->name('reports.appointments.export');
    Route::get('/reports/appointments/export', [ReportsController::class, 'exportAppointments']);
    Route::get('/reports/system', [ReportsController::class, 'systemReport'])->name('reports.system');
    Route::post('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
});


