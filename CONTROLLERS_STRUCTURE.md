# Controllers Structure - Admin, Doctor, Patient

## 📂 Complete Controller Hierarchy

```
app/Http/Controllers/
├── LoginController.php                          ← Authentication
├── DashboardController.php                      ← General dashboard
├── Admin/
│   ├── DashboardController.php                 ← Admin dashboard
│   ├── UserController.php                      ← User management
│   ├── SettingsController.php                  ← System settings
│   └── ReportsController.php                   ← System reports
├── Doctor/
│   ├── DashboardController.php                 ← Doctor dashboard
│   ├── PatientController.php                   ← Patient management
│   ├── AppointmentController.php               ← Appointment management
│   └── PrescriptionController.php              ← Prescription management
└── Patient/
    ├── DashboardController.php                 ← Patient dashboard
    ├── AppointmentController.php               ← Book/view appointments
    ├── MedicalHistoryController.php            ← Medical records
    └── PrescriptionController.php              ← View prescriptions
```

## 🔑 Admin Controllers

### AdminDashboardController
**File:** `app/Http/Controllers/Admin/DashboardController.php`
**Purpose:** Display admin dashboard
**Methods:**
- `__invoke()` - Render admin dashboard

**Route:** `GET /admin/dashboard`

### UserController
**File:** `app/Http/Controllers/Admin/UserController.php`
**Purpose:** Manage system users
**Methods:**
- `index()` - List all users
- `create()` - Show user creation form
- `store()` - Save new user
- `show()` - View user details
- `edit()` - Show edit form
- `update()` - Update user
- `destroy()` - Delete user

**Routes:**
```
GET  /admin/users              → index
GET  /admin/users/create       → create
POST /admin/users              → store
GET  /admin/users/{user}       → show
GET  /admin/users/{user}/edit  → edit
PUT  /admin/users/{user}       → update
DELETE /admin/users/{user}     → destroy
```

### SettingsController
**File:** `app/Http/Controllers/Admin/SettingsController.php`
**Purpose:** Manage system settings
**Methods:**
- `index()` - Show settings page
- `update()` - Update settings

**Routes:**
```
GET  /admin/settings    → index
POST /admin/settings    → update
```

**Settings Available:**
- App name
- App URL
- Timezone
- Maintenance mode
- Max users

### ReportsController
**File:** `app/Http/Controllers/Admin/ReportsController.php`
**Purpose:** Generate system reports
**Methods:**
- `index()` - Show reports dashboard
- `userReport()` - User statistics report
- `systemReport()` - System health report
- `export()` - Export report

**Routes:**
```
GET  /admin/reports         → index
GET  /admin/reports/users   → userReport
GET  /admin/reports/system  → systemReport
POST /admin/reports/export  → export
```

## 👨‍⚕️ Doctor Controllers

### DoctorDashboardController
**File:** `app/Http/Controllers/Doctor/DashboardController.php`
**Purpose:** Display doctor dashboard
**Methods:**
- `__invoke()` - Render doctor dashboard

**Route:** `GET /doctor/dashboard`

### PatientController
**File:** `app/Http/Controllers/Doctor/PatientController.php`
**Purpose:** Manage doctor's patients
**Methods:**
- `index()` - List doctor's patients
- `show()` - View patient details
- `addNote()` - Add clinical notes
- `medicalHistory()` - View patient history

**Routes:**
```
GET  /doctor/patients               → index
GET  /doctor/patients/{patient}     → show
POST /doctor/patients/{patient}/notes → addNote
GET  /doctor/patients/{patient}/history → medicalHistory
```

**Note Types:**
- Diagnosis
- Treatment
- Observation

### AppointmentController
**File:** `app/Http/Controllers/Doctor/AppointmentController.php`
**Purpose:** Manage doctor's appointments
**Methods:**
- `index()` - View appointments
- `show()` - View appointment details
- `updateStatus()` - Update appointment status
- `cancel()` - Cancel appointment

**Routes:**
```
GET  /doctor/appointments                → index
GET  /doctor/appointments/{id}           → show
PUT  /doctor/appointments/{id}/status    → updateStatus
DELETE /doctor/appointments/{id}         → cancel
```

**Status Options:**
- scheduled
- completed
- cancelled
- no-show

### PrescriptionController
**File:** `app/Http/Controllers/Doctor/PrescriptionController.php`
**Purpose:** Manage patient prescriptions
**Methods:**
- `index()` - List prescriptions
- `create()` - Create prescription form
- `store()` - Save new prescription
- `show()` - View prescription
- `edit()` - Edit form
- `cancel()` - Cancel prescription

**Routes:**
```
GET  /doctor/prescriptions              → index
GET  /doctor/prescriptions/create       → create
POST /doctor/prescriptions              → store
GET  /doctor/prescriptions/{id}         → show
GET  /doctor/prescriptions/{id}/edit    → edit
DELETE /doctor/prescriptions/{id}       → cancel
```

## 🏥 Patient Controllers

### PatientDashboardController
**File:** `app/Http/Controllers/Patient/DashboardController.php`
**Purpose:** Display patient dashboard
**Methods:**
- `__invoke()` - Render patient dashboard

**Route:** `GET /patient/dashboard`

### AppointmentController
**File:** `app/Http/Controllers/Patient/AppointmentController.php`
**Purpose:** Patient appointment management
**Methods:**
- `index()` - List appointments
- `create()` - Booking form
- `store()` - Book appointment
- `cancel()` - Cancel appointment
- `reschedule()` - Reschedule appointment

**Routes:**
```
GET  /patient/appointments           → index
GET  /patient/appointments/create    → create
POST /patient/appointments           → store
DELETE /patient/appointments/{id}    → cancel
PUT  /patient/appointments/{id}      → reschedule
```

### MedicalHistoryController
**File:** `app/Http/Controllers/Patient/MedicalHistoryController.php`
**Purpose:** View medical records and history
**Methods:**
- `index()` - List medical history
- `show()` - View detailed record
- `download()` - Download records
- `requestRecords()` - Request from other providers

**Routes:**
```
GET  /patient/medical-history           → index
GET  /patient/medical-history/{id}      → show
GET  /patient/medical-history/{id}/download → download
POST /patient/medical-history/request   → requestRecords
```

### PrescriptionController
**File:** `app/Http/Controllers/Patient/PrescriptionController.php`
**Purpose:** View and manage patient prescriptions
**Methods:**
- `index()` - List prescriptions
- `show()` - View prescription details
- `requestRefill()` - Request refill from doctor
- `download()` - Download prescription
- `shareWithPharmacy()` - Share with pharmacy

**Routes:**
```
GET  /patient/prescriptions                    → index
GET  /patient/prescriptions/{id}               → show
POST /patient/prescriptions/{id}/refill        → requestRefill
GET  /patient/prescriptions/{id}/download      → download
POST /patient/prescriptions/{id}/share-pharmacy → shareWithPharmacy
```

## 🗂️ Folder Organization

### Admin Folder (`app/Http/Controllers/Admin/`)
All controllers related to system administration:
- Dashboard management
- User CRUD operations
- System configuration
- Reporting and analytics

### Doctor Folder (`app/Http/Controllers/Doctor/`)
All controllers for doctor portal functionality:
- Patient management
- Appointment scheduling
- Prescription writing
- Medical notes

### Patient Folder (`app/Http/Controllers/Patient/`)
All controllers for patient portal functionality:
- Appointment booking
- Medical records access
- Prescription viewing
- Health management

## 📋 Standard Methods

### Resource-Based Methods (CRUD)
- `index()` - List all resources
- `create()` - Show creation form
- `store()` - Save new resource
- `show()` - View single resource
- `edit()` - Show edit form
- `update()` - Update resource
- `destroy()` - Delete resource

### Custom Methods
- Additional methods for specific functionality
- Named after actions (e.g., `cancel()`, `refill()`)

## 🔐 Authorization

All controllers inherit from `Controller` base class and should use middleware:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    // Doctor routes
});

Route::middleware(['auth', 'role:patient'])->group(function () {
    // Patient routes
});
```

## 💡 Usage Examples

### Admin Usage
```php
// In controller
return Inertia::render('Admin/Users/Index', [
    'users' => User::paginate(15),
]);

// In routes
Route::resource('admin/users', UserController::class);
```

### Doctor Usage
```php
// In controller
$patients = User::where('role', 'patient')->paginate(15);
return Inertia::render('Doctor/Patients/Index', [
    'patients' => $patients,
]);
```

### Patient Usage
```php
// In controller
$appointments = Appointment::where('patient_id', auth()->id())->get();
return Inertia::render('Patient/Appointments/Index', [
    'appointments' => $appointments,
]);
```

## 🚀 Quick Integration

To use these controllers in routes:

```php
// admin/users
Route::resource('admin/users', App\Http\Controllers\Admin\UserController::class);

// doctor/patients
Route::resource('doctor/patients', App\Http\Controllers\Doctor\PatientController::class);

// patient/appointments
Route::resource('patient/appointments', App\Http\Controllers\Patient\AppointmentController::class);
```

## 🔄 Next Steps

1. Create corresponding Inertia Vue components for each controller
2. Add database models and migrations
3. Implement validation rules
4. Add authorization policies
5. Create database seeders with test data
6. Build API endpoints if needed

---

**All 11 controllers are now organized in 3 role-based folders!** ✅
