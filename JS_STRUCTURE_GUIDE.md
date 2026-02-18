# JavaScript Structure - Complete & Organized ✅

## Folder Structure - Organized by Role

```
resources/js/
├── Admin/                         # Admin JS Application
│   ├── app.js                     # Admin Inertia App Entry Point
│   ├── Components/
│   │   └── StatsCard.vue          # Reusable stats card component
│   ├── Layouts/
│   │   └── AdminLayout.vue        # Admin sidebar + header layout
│   └── Pages/
│       ├── Dashboard.vue          # Admin Dashboard
│       └── Login.vue              # Admin Login
│
├── Doctor/                        # Doctor JS Application
│   ├── app.js                     # Doctor Inertia App Entry Point
│   ├── Components/
│   │   └── StatsCard.vue          # Reusable stats card component
│   ├── Layouts/
│   │   └── DoctorLayout.vue       # Doctor sidebar + header layout
│   └── Pages/
│       ├── Dashboard.vue          # Doctor Dashboard
│       └── Login.vue              # Doctor Login
│
├── Patient/                       # Patient JS Application
│   ├── app.js                     # Patient Inertia App Entry Point
│   ├── Components/
│   │   └── StatsCard.vue          # Reusable stats card component
│   ├── Layouts/
│   │   └── PatientLayout.vue      # Patient sidebar + header layout
│   └── Pages/
│       ├── Dashboard.vue          # Patient Dashboard
│       └── Login.vue              # Patient Login
│
├── app.js                         # Legacy main entry point (optional)
├── bootstrap.js                   # Vue/Inertia bootstrap config
├── Components/                    # (DEPRECATED - use role-specific)
├── Layouts/                       # (DEPRECATED - use role-specific)
└── Pages/                         # (DEPRECATED - use role-specific)
```

## Key Features

### 1. **Isolated Applications**
Each role has its own complete Inertia.js application:
- Independent app.js for each role
- Role-specific page resolution
- Isolated component dependencies

### 2. **Consistent Structure**
All three roles follow the exact same pattern:
```
Role/
├── app.js
├── Components/
├── Layouts/
└── Pages/
```

### 3. **Color Coded Themes**
- **Admin**: Red (#ef4444)
- **Doctor**: Blue (#3b82f6)  
- **Patient**: Green (#10b981)

## How Routes Work

### Example 1: Admin Dashboard
**Route**: `/admin/dashboard`
**Controller**: `App\Http\Controllers\Admin\DashboardController`
**Blade Template**: `resources/views/admin.blade.php`
**App Entry**: `resources/js/Admin/app.js`
**Page Component**: `resources/js/Admin/Pages/Dashboard.vue`

```php
// In Controller
return Inertia::render('Dashboard', [...]);
```

### Example 2: Doctor Appointments
**Route**: `/doctor/appointments`
**Controller**: `App\Http\Controllers\Doctor\AppointmentController`
**Blade Template**: `resources/views/doctor.blade.php`
**App Entry**: `resources/js/Doctor/app.js`
**Page Component**: `resources/js/Doctor/Pages/Appointments.vue`

### Example 3: Patient Medical History
**Route**: `/patient/medical-history`
**Controller**: `App\Http\Controllers\Patient\MedicalHistoryController`
**Blade Template**: `resources/views/patient.blade.php`
**App Entry**: `resources/js/Patient/app.js`
**Page Component**: `resources/js/Patient/Pages/MedicalHistory.vue`

## Creating New Pages

### For Admin
1. Create file: `resources/js/Admin/Pages/MyPage.vue`
2. Import layout: `import AdminLayout from '../Layouts/AdminLayout.vue'`
3. In Controller: `Inertia::render('MyPage', [...])`

### For Doctor
1. Create file: `resources/js/Doctor/Pages/MyPage.vue`
2. Import layout: `import DoctorLayout from '../Layouts/DoctorLayout.vue'`
3. In Controller: `Inertia::render('MyPage', [...])`

### For Patient
1. Create file: `resources/js/Patient/Pages/MyPage.vue`
2. Import layout: `import PatientLayout from '../Layouts/PatientLayout.vue'`
3. In Controller: `Inertia::render('MyPage', [...])`

## File References in Components

### Importing from Same Role
```vue
<script setup>
import AdminLayout from '../Layouts/AdminLayout.vue';
import StatsCard from '../Components/StatsCard.vue';
</script>
```

### Importing Bootstrap/Config
```vue
<script setup>
// From Admin/app.js
import '../bootstrap';
</script>
```

## Vite Configuration

The `vite.config.js` includes all three entry points:

```javascript
input: [
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/js/Admin/app.js',     // Admin app
    'resources/js/Doctor/app.js',    // Doctor app
    'resources/js/Patient/app.js',   // Patient app
]
```

This ensures all three applications are built separately with their own bundles.

## Blade Templates

Each role has its own Blade template that loads the correct app:

- `resources/views/admin.blade.php` → loads `resources/js/Admin/app.js`
- `resources/views/doctor.blade.php` → loads `resources/js/Doctor/app.js`
- `resources/views/patient.blade.php` → loads `resources/js/Patient/app.js`

The `RoleBasedLayout` middleware automatically selects the correct template based on user role.

## Build Process

```bash
# Development
npm run dev

# Production
npm run build
```

This will generate:
- `/public/build/assets/app-*.js` (shared assets)
- `/public/build/assets/Admin/app-*.js` (Admin app)
- `/public/build/assets/Doctor/app-*.js` (Doctor app)
- `/public/build/assets/Patient/app-*.js` (Patient app)

## Common Tasks

### Add a new page to Admin
```bash
# Create the component
touch resources/js/Admin/Pages/UserManagement.vue

# In your admin controller
return Inertia::render('UserManagement', ['users' => $users]);
```

### Add a new component to Doctor
```bash
# Create the component
touch resources/js/Doctor/Components/PatientCard.vue

# Use in Doctor pages
import PatientCard from '../Components/PatientCard.vue';
```

### Import shared utilities
If you need to import something from outside your role folder:
```vue
<script setup>
// Go up two levels to root, then into another folder
import SomeUtility from '../../utilities/helper.js';
</script>
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Page not found | Check page is in correct role folder (Pages/) |
| Layout not showing | Verify Layout import path is correct (../Layouts/) |
| Component not found | Ensure component is in Components/ folder |
| Styling not working | Run `npm run build` to recompile |
| Wrong theme showing | Check `RoleBasedLayout` middleware is installed |

## Summary

✅ Each role has completely isolated JS structure
✅ Identical organization patterns across all three roles  
✅ Clear separation of concerns
✅ Easy to extend and maintain
✅ Role-specific styling and branding
✅ Component reusability within role scope
