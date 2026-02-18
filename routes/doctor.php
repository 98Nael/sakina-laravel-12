<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\PrescriptionController as DoctorPrescriptionController;
use App\Http\Controllers\Doctor\ProfileController;

// ========================================
// 👨‍⚕️ DOCTOR ROUTES (بريفكس: /doctor)
// ========================================
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    
    // لوحة التحكم
    Route::get('/dashboard', DoctorDashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // إدارة المرضى
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::post('/patients/{patient}/notes', [PatientController::class, 'addNote'])->name('patients.addNote');
    Route::get('/patients/{patient}/history', [PatientController::class, 'medicalHistory'])->name('patients.medicalHistory');
    Route::get('/patients/{patient}/records', [PatientController::class, 'records'])->name('patients.records');
    
    // المواعيد
    Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/calendar', [DoctorAppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
    Route::put('/appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::delete('/appointments/{appointment}', [DoctorAppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // الروشتات الطبية
    Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/create', [DoctorPrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/{prescription}/edit', [DoctorPrescriptionController::class, 'edit'])->name('prescriptions.edit');
    Route::put('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::delete('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'cancel'])->name('prescriptions.cancel');
    
    // السجلات الطبية
    Route::get('/medical-records', function () {
        return redirect()->route('doctor.patients.index');
    })->name('medical_records.index');
    Route::post('/medical-records', [PatientController::class, 'createRecord'])->name('medical_records.store');
});
