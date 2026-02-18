<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\MedicalHistoryController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;
use App\Http\Controllers\Patient\ProfileController;

// ========================================
// 🏥 PATIENT ROUTES (بريفكس: /patient)
// ========================================
Route::middleware(['auth', 'role:patient', 'inertia_patient'])->prefix('patient')->name('patient.')->group(function () {
    
    // لوحة التحكم
    Route::get('/dashboard', PatientDashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // المواعيد
    Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [PatientAppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [PatientAppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [PatientAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // السجل الطبي
    Route::get('/medical-history', [MedicalHistoryController::class, 'index'])->name('medical_history.index');
    Route::get('/medical-history/{record}', [MedicalHistoryController::class, 'show'])->name('medical_history.show');
    Route::get('/medical-history/{record}/download', [MedicalHistoryController::class, 'download'])->name('medical_history.download');
    Route::post('/medical-history/request', [MedicalHistoryController::class, 'requestRecords'])->name('medical_history.request');
    
    // الروشتات الطبية
    Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [PatientPrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/{prescription}/download', [PatientPrescriptionController::class, 'download'])->name('prescriptions.download');
    Route::post('/prescriptions/{prescription}/refill', [PatientPrescriptionController::class, 'requestRefill'])->name('prescriptions.refill');
    Route::post('/prescriptions/{prescription}/share-pharmacy', [PatientPrescriptionController::class, 'shareWithPharmacy'])->name('prescriptions.sharePharmacy');
    
    // الأطباء المعينين
    Route::get('/doctors', [PatientAppointmentController::class, 'doctors'])->name('doctors.index');
    Route::get('/doctors/{doctor}', [PatientAppointmentController::class, 'doctorProfile'])->name('doctors.show');
});
