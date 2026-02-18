# Role-Based Layout Configuration

## Overview
Each user role (Admin, Doctor, Patient) now has its own dedicated Inertia.js application and layout template.

## Structure

```
resources/
├── js/
│   ├── apps/
│   │   ├── admin.js       # Admin app entry point
│   │   ├── doctor.js      # Doctor app entry point
│   │   └── patient.js     # Patient app entry point
│   ├── Layouts/
│   │   ├── AdminLayout.vue     # Admin dashboard layout
│   │   ├── DoctorLayout.vue    # Doctor dashboard layout
│   │   └── PatientLayout.vue   # Patient dashboard layout
│   └── Pages/
│       ├── Admin/         # Admin pages
│       │   └── Dashboard.vue
│       ├── Doctor/        # Doctor pages
│       │   └── Dashboard.vue
│       └── Patient/       # Patient pages
│           └── Dashboard.vue
└── views/
    ├── admin.blade.php    # Admin template
    ├── doctor.blade.php   # Doctor template
    └── patient.blade.php  # Patient template
```

## How It Works

### 1. User Authentication
When a user logs in, the middleware `RoleBasedLayout` automatically detects their role and selects the appropriate Blade template:

- **Admin users** → Uses `admin.blade.php` and loads `resources/js/apps/admin.js`
- **Doctor users** → Uses `doctor.blade.php` and loads `resources/js/apps/doctor.js`
- **Patient users** → Uses `patient.blade.php` and loads `resources/js/apps/patient.js`

### 2. Page Resolution
Each app resolves pages from its own directory:
- Admin app looks for pages in `Pages/Admin/`
- Doctor app looks for pages in `Pages/Doctor/`
- Patient app looks for pages in `Pages/Patient/`

## Creating Pages for Each Role

### For Admin Pages
Create Vue components in `resources/js/Pages/Admin/`

Example: `resources/js/Pages/Admin/UserManagement.vue`
```vue
<template>
    <AdminLayout>
        <template #header>User Management</template>
        <!-- Your content here -->
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>
```

### For Doctor Pages
Create Vue components in `resources/js/Pages/Doctor/`

Example: `resources/js/Pages/Doctor/Patients.vue`
```vue
<template>
    <DoctorLayout>
        <template #header>My Patients</template>
        <!-- Your content here -->
    </DoctorLayout>
</template>

<script setup>
import DoctorLayout from '@/Layouts/DoctorLayout.vue';
</script>
```

### For Patient Pages
Create Vue components in `resources/js/Pages/Patient/`

Example: `resources/js/Pages/Patient/Appointments.vue`
```vue
<template>
    <PatientLayout>
        <template #header>My Appointments</template>
        <!-- Your content here -->
    </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
</script>
```

## Controller Examples

### Admin Controller
```php
<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;

class DashboardController
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'totalUsers' => User::count(),
                'doctors' => User::where('role', 'doctor')->count(),
                'patients' => User::where('role', 'patient')->count(),
            ]
        ]);
    }
}
```

### Doctor Controller
```php
<?php

namespace App\Http\Controllers\Doctor;

use Inertia\Inertia;

class DashboardController
{
    public function __invoke()
    {
        $doctor = auth()->user();
        
        return Inertia::render('Dashboard', [
            'appointments' => $doctor->appointments()->today()->get(),
        ]);
    }
}
```

### Patient Controller
```php
<?php

namespace App\Http\Controllers\Patient;

use Inertia\Inertia;

class DashboardController
{
    public function __invoke()
    {
        $patient = auth()->user();
        
        return Inertia::render('Dashboard', [
            'nextAppointment' => $patient->appointments()->next()->first(),
        ]);
    }
}
```

## Styling & Colors

Each layout has its own color scheme:
- **Admin**: Red accent color (#ef4444)
- **Doctor**: Blue accent color (#3b82f6)
- **Patient**: Green accent color (#10b981)

You can customize these colors in `resources/js/Layouts/[Role]Layout.vue`.

## Migration from Old Structure

If you have existing pages, you need to:

1. Move them to the appropriate role directory
   ```
   Pages/Old_Page.vue → Pages/Admin/OldPage.vue (for admin pages)
   ```

2. Update the component to use the correct layout
   ```vue
   <template>
       <AdminLayout><!-- Your content --></AdminLayout>
   </template>
   ```

3. No changes needed in your controllers - this happens automatically!

## Testing

1. Login as an **Admin**:
   - Should see the red admin dashboard
   - Sidebar shows admin-specific menu

2. Login as a **Doctor**:
   - Should see the blue doctor dashboard
   - Sidebar shows doctor-specific menu

3. Login as a **Patient**:
   - Should see the green patient dashboard
   - Sidebar shows patient-specific menu

## Important Notes

- The role detection happens automatically via `RoleBasedLayout` middleware
- Page components should be placed in their role-specific directory
- Always wrap pages with the appropriate layout component
- The `@inertiaHead` and `@inertia` directives are handled by the Blade templates
