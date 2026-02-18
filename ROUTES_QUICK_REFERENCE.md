# Routes Quick Reference Card

## 🎯 Start Here

### Access Your Dashboards
```
Admin Dashboard:    http://localhost:8000/admin/dashboard
Doctor Dashboard:   http://localhost:8000/doctor/dashboard
Patient Dashboard:  http://localhost:8000/patient/dashboard
```

### Login With
```
Admin:   admin@example.com / password
Doctor:  doctor@example.com / password
Patient: patient@example.com / password
```

---

## 👨‍💼 ADMIN ROUTES

| URL | Name | Purpose |
|-----|------|---------|
| `/admin/dashboard` | `admin.dashboard` | Dashboard view |
| `/admin/users` | `admin.users.index` | List all users |
| `/admin/users/create` | `admin.users.create` | Create user form |
| `/admin/users/{id}` | `admin.users.show` | User details |
| `/admin/users/{id}/edit` | `admin.users.edit` | Edit user form |
| `/admin/settings` | `admin.settings.index` | System settings |
| `/admin/reports` | `admin.reports.index` | Reports dashboard |
| `/admin/reports/users` | `admin.reports.users` | User reports |
| `/admin/reports/system` | `admin.reports.system` | System reports |

---

## 👨‍⚕️ DOCTOR ROUTES

| URL | Name | Purpose |
|-----|------|---------|
| `/doctor/dashboard` | `doctor.dashboard` | Dashboard view |
| `/doctor/patients` | `doctor.patients.index` | List patients |
| `/doctor/patients/{id}` | `doctor.patients.show` | Patient details |
| `/doctor/patients/{id}/history` | `doctor.patients.medicalHistory` | Medical history |
| `/doctor/appointments` | `doctor.appointments.index` | My appointments |
| `/doctor/appointments/{id}` | `doctor.appointments.show` | Appointment details |
| `/doctor/prescriptions` | `doctor.prescriptions.index` | My prescriptions |
| `/doctor/prescriptions/create` | `doctor.prescriptions.create` | Write prescription |
| `/doctor/prescriptions/{id}` | `doctor.prescriptions.show` | Prescription details |

---

## 🏥 PATIENT ROUTES

| URL | Name | Purpose |
|-----|------|---------|
| `/patient/dashboard` | `patient.dashboard` | Dashboard view |
| `/patient/appointments` | `patient.appointments.index` | My appointments |
| `/patient/appointments/create` | `patient.appointments.create` | Book appointment |
| `/patient/appointments/{id}` | `patient.appointments.show` | Appointment details |
| `/patient/medical-history` | `patient.medical_history.index` | Medical history |
| `/patient/medical-history/{id}` | `patient.medical_history.show` | Record details |
| `/patient/prescriptions` | `patient.prescriptions.index` | My prescriptions |
| `/patient/prescriptions/{id}` | `patient.prescriptions.show` | Prescription details |

---

## 🔗 Navigation Commands

### Generate URL in Blade
```blade
<a href="{{ route('admin.users.index') }}">Users</a>
<a href="{{ route('doctor.patients.index') }}">Patients</a>
<a href="{{ route('patient.appointments.index') }}">Appointments</a>
```

### Generate URL in Controller
```php
return redirect()->route('admin.users.index');
return redirect()->route('doctor.patients.create');
return redirect()->route('patient.prescriptions.show', $prescriptionId);
```

### Generate URL in Vue Component
```vue
<Link href="/admin/users">Users List</Link>
```

---

## 📊 Route Statistics

- **Total Routes**: 63
- **Admin Routes**: 18
- **Doctor Routes**: 16
- **Patient Routes**: 16
- **Authentication Routes**: 3
- **Public Routes**: 1

---

## 🛡️ Access Control

### Everything is Protected!
- ✅ All role routes require `auth` middleware
- ✅ All role routes require `role` middleware
- ✅ Wrong role = 403 Forbidden
- ✅ Not logged in = Redirect to login

### Try These to Test
```
1. Logout and try accessing any role route
   → Redirects to /login

2. Login as patient, try visiting /admin/users
   → 403 Forbidden error

3. Login as admin, try visiting /doctor/patients
   → 403 Forbidden error
```

---

## 🧪 CLI Commands

### List all routes
```bash
php artisan route:list
```

### List specific role routes
```bash
php artisan route:list | grep admin
php artisan route:list | grep doctor
php artisan route:list | grep patient
```

### Check route details
```bash
php artisan route:list --method=GET
php artisan route:list --method=POST
```

---

## ✨ Key Features

✅ **Organized by Role** - Admin, Doctor, Patient
✅ **Middleware Protected** - Auth + Role required
✅ **RESTful Design** - Standard HTTP verbs
✅ **Named Routes** - Easy URL generation
✅ **Prefixed Groups** - Clean URL structure
✅ **Comprehensive** - 63 routes total

---

## 🚀 File Location

Main Routes File: `routes/web.php`

View with:
```bash
cat routes/web.php
# or in VS Code, open file
```

---

## 📚 Related Documentation

- [ROUTES_SETUP.md](ROUTES_SETUP.md) - Complete route documentation
- [CONTROLLERS_STRUCTURE.md](CONTROLLERS_STRUCTURE.md) - Controller details
- [ROUTES_EXAMPLES.md](ROUTES_EXAMPLES.md) - Route examples
- [DASHBOARDS_SETUP.md](DASHBOARDS_SETUP.md) - Dashboard setup

---

**All routes are ready to use! Start by logging in and navigating to your role's dashboard.** 🎉
