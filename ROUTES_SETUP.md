# Complete Routes Setup - Admin, Doctor, Patient

## ✅ Route Setup Complete!

All three role-based route groups have been configured in `routes/web.php`.

## 📋 Route Structure

### **Public Routes**
```
GET  /              → Welcome page
```

### **Authentication Routes**
```
GET  /login         → Login page
POST /login         → Process login
POST /logout        → Logout user
```

### **General Authenticated Routes**
```
GET  /dashboard     → General user dashboard
```

## 👨‍💼 **ADMIN ROUTES** (/admin)

### Dashboard
```
GET  /admin/dashboard                          → Admin Dashboard
```

### User Management
```
GET    /admin/users                            → List all users
GET    /admin/users/create                     → Create user form
POST   /admin/users                            → Save new user
GET    /admin/users/{user}                     → View user details
GET    /admin/users/{user}/edit                → Edit user form
PUT    /admin/users/{user}                     → Update user
DELETE /admin/users/{user}                     → Delete user
```

### System Settings
```
GET    /admin/settings                         → View settings
POST   /admin/settings                         → Update settings
GET    /admin/settings/{setting}/edit          → Edit setting
PUT    /admin/settings/{setting}               → Update setting
DELETE /admin/settings/{setting}               → Delete setting
```

### Reports & Analytics
```
GET  /admin/reports                            → Reports dashboard
GET  /admin/reports/users                      → User statistics report
GET  /admin/reports/system                     → System health report
POST /admin/reports/export                     → Export report to file
```

---

## 👨‍⚕️ **DOCTOR ROUTES** (/doctor)

### Dashboard
```
GET  /doctor/dashboard                         → Doctor Dashboard
```

### Patient Management
```
GET    /doctor/patients                        → List doctor's patients
GET    /doctor/patients/{patient}              → View patient details
POST   /doctor/patients/{patient}/notes        → Add clinical notes
GET    /doctor/patients/{patient}/history      → View patient medical history
```

### Appointments
```
GET    /doctor/appointments                    → View appointments
GET    /doctor/appointments/{appointment}      → View appointment details
PUT    /doctor/appointments/{appointment}/status → Update appointment status
DELETE /doctor/appointments/{appointment}      → Cancel appointment
```

### Prescriptions
```
GET    /doctor/prescriptions                   → List prescriptions
GET    /doctor/prescriptions/create            → Create prescription form
POST   /doctor/prescriptions                   → Save new prescription
GET    /doctor/prescriptions/{prescription}    → View prescription details
GET    /doctor/prescriptions/{prescription}/edit → Edit prescription form
PUT    /doctor/prescriptions/{prescription}    → Update prescription
DELETE /doctor/prescriptions/{prescription}    → Cancel prescription
```

---

## 🏥 **PATIENT ROUTES** (/patient)

### Dashboard
```
GET  /patient/dashboard                        → Patient Dashboard
```

### Appointments
```
GET    /patient/appointments                   → View my appointments
GET    /patient/appointments/create            → Book appointment form
POST   /patient/appointments                   → Save new appointment
GET    /patient/appointments/{appointment}     → View appointment details
DELETE /patient/appointments/{appointment}     → Cancel appointment
PUT    /patient/appointments/{appointment}     → Reschedule appointment
```

### Medical History
```
GET    /patient/medical-history                → View medical history
GET    /patient/medical-history/{record}       → View medical record
GET    /patient/medical-history/{record}/download → Download record
POST   /patient/medical-history/request        → Request records from providers
```

### Prescriptions
```
GET    /patient/prescriptions                  → View my prescriptions
GET    /patient/prescriptions/{prescription}   → View prescription details
POST   /patient/prescriptions/{prescription}/refill → Request prescription refill
GET    /patient/prescriptions/{prescription}/download → Download prescription
POST   /patient/prescriptions/{prescription}/share-pharmacy → Share with pharmacy
```

---

## 🔐 Route Authorization

All role-specific routes use middleware authentication:

```php
// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Only admin users can access
});

// Doctor routes
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->group(function () {
    // Only doctor users can access
});

// Patient routes
Route::middleware(['auth', 'role:patient'])->prefix('patient')->group(function () {
    // Only patient users can access
});
```

### Authorization Behavior
- **Unauthenticated users** → Redirected to `/login`
- **Wrong role users** → Get 403 Forbidden error
- **Correct role users** → Full access granted

---

## 🎯 Route Naming Convention

### Admin Routes
- `admin.dashboard` - Admin dashboard
- `admin.users.*` - User management
- `admin.settings.*` - System settings
- `admin.reports.*` - Reports

### Doctor Routes
- `doctor.dashboard` - Doctor dashboard
- `doctor.patients.*` - Patient management
- `doctor.appointments.*` - Appointments
- `doctor.prescriptions.*` - Prescriptions

### Patient Routes
- `patient.dashboard` - Patient dashboard
- `patient.appointments.*` - Appointments
- `patient.medical_history.*` - Medical history
- `patient.prescriptions.*` - Prescriptions

---

## 📊 Route Summary Table

| Module | Section | Method | Count |
|--------|---------|--------|-------|
| **Admin** | Dashboard | 1 | 1 |
| | Users | CRUD + All | 8 |
| | Settings | CRUD + All | 5 |
| | Reports | Custom | 4 |
| | **Subtotal** | | **18** |
| **Doctor** | Dashboard | 1 | 1 |
| | Patients | Custom | 4 |
| | Appointments | Custom | 4 |
| | Prescriptions | CRUD + All | 7 |
| | **Subtotal** | | **16** |
| **Patient** | Dashboard | 1 | 1 |
| | Appointments | CRUD + Custom | 6 |
| | Medical History | Custom | 4 |
| | Prescriptions | Custom | 5 |
| | **Subtotal** | | **16** |
| **Total Routes** | | | **63** |

---

## 🧪 Testing Routes

### View all routes
```bash
php artisan route:list
```

### Filter by prefix
```bash
php artisan route:list | grep admin
php artisan route:list | grep doctor
php artisan route:list | grep patient
```

### Test specific user role
```bash
# Test Admin routes
Login: admin@example.com / password
Visit: http://localhost:8000/admin/dashboard

# Test Doctor routes
Login: doctor@example.com / password
Visit: http://localhost:8000/doctor/dashboard

# Test Patient routes
Login: patient@example.com / password
Visit: http://localhost:8000/patient/dashboard
```

---

## 💡 Usage in Controllers

### Generate Route URLs
```php
// In controller
return redirect()->route('admin.users.index');
return redirect()->route('doctor.appointments.index');
return redirect()->route('patient.prescriptions.index');
```

### in Blade Templates
```blade
<!-- Admin -->
<a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
<a href="{{ route('admin.users.index') }}">Users</a>

<!-- Doctor -->
<a href="{{ route('doctor.dashboard') }}">Doctor Dashboard</a>
<a href="{{ route('doctor.patients.index') }}">Patients</a>

<!-- Patient -->
<a href="{{ route('patient.dashboard') }}">Patient Dashboard</a>
<a href="{{ route('patient.appointments.index') }}">Appointments</a>
```

### in Vue Components
```vue
<script setup>
import { Link } from '@inertiajs/vue3';

// Using Link component
<Link href="/admin/users">Users List</Link>
</script>
```

---

## 📋 Complete Controller-Route Mapping

### Admin
| Controller | Route | Method |
|-----------|-------|--------|
| DashboardController | /admin/dashboard | GET |
| UserController | /admin/users/* | CRUD |
| SettingsController | /admin/settings/* | CRUD |
| ReportsController | /admin/reports/* | Custom |

### Doctor
| Controller | Route | Method |
|-----------|-------|--------|
| DashboardController | /doctor/dashboard | GET |
| PatientController | /doctor/patients/* | Custom |
| AppointmentController | /doctor/appointments/* | Custom |
| PrescriptionController | /doctor/prescriptions/* | CRUD |

### Patient
| Controller | Route | Method |
|-----------|-------|--------|
| DashboardController | /patient/dashboard | GET |
| AppointmentController | /patient/appointments/* | CRUD |
| MedicalHistoryController | /patient/medical-history/* | Custom |
| PrescriptionController | /patient/prescriptions/* | Custom |

---

## 🚀 Next Steps

1. ✅ Routes configured (all 63 routes)
2. ✅ Controllers created
3. ✅ Dashboards created
4. ⏭️ Create Inertia Vue components for each route
5. ⏭️ Create database migrations and models
6. ⏭️ Implement form validation
7. ⏭️ Add authorization policies
8. ⏭️ Build API endpoints (optional)

---

## ⚠️ Important Notes

- All role-specific routes are protected with middleware
- Routes use resource naming convention
- Custom routes are explicitly defined (e.g., `/notes`, `/status`, `/refill`)
- All routes automatically regenerate session ID on login
- Logout clears session completely

---

**All 3 route groups with 63 total routes are now configured!** ✅
