# Role-Based Dashboards Setup Guide

## ✅ What's Been Created

### Dashboard Pages
1. **Admin Dashboard** - `resources/js/Pages/Admin/Dashboard.vue`
   - User management section
   - System statistics (total users, doctors, patients, status)
   - Quick actions (reports, database, logs)
   - Recent activities table
   - Dark themed interface with purple accents

2. **Doctor Dashboard** - `resources/js/Pages/Doctor/Dashboard.vue`
   - My Patients section
   - Today's schedule
   - Medical statistics (patient count, appointments, pending reports)
   - Patient activity tracking
   - Green themed interface

3. **Patient Dashboard** - `resources/js/Pages/Patient/Dashboard.vue`
   - Health summary (blood pressure, heart rate, appointments, prescriptions)
   - Medical records access
   - Upcoming appointments table
   - Primary doctor information
   - Blue themed interface

### Controllers
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Doctor/DashboardController.php`
- `app/Http/Controllers/Patient/DashboardController.php`

### Routes
All routes are protected with role-based middleware:
- `/admin/dashboard` - Admin access only
- `/doctor/dashboard` - Doctor access only
- `/patient/dashboard` - Patient access only

## 🔗 URL Routes

| URL | Role Required | Purpose |
|-----|---------------|---------|
| `/login` | Guest only | Login page |
| `/dashboard` | Authenticated | General dashboard |
| `/admin/dashboard` | Admin | Admin control panel |
| `/doctor/dashboard` | Doctor | Doctor portal |
| `/patient/dashboard` | Patient | Patient health portal |

## 👥 Test Credentials

```
Admin:
  Email: admin@example.com
  Password: password
  
Doctor:
  Email: doctor@example.com
  Password: password
  
Patient:
  Email: patient@example.com
  Password: password
  
Regular User:
  Email: test@example.com
  Password: password
```

## 🚀 Quick Start

### Start Development Servers

**Option 1 - Using Composer dev command:**
```bash
composer dev
```

**Option 2 - Manual startup (two terminals):**

Terminal 1:
```bash
php artisan serve
```

Terminal 2:
```bash
npm run dev
```

### Test the System

1. Go to `http://localhost:8000/login`
2. Login with credentials above
3. You'll be redirected to your role-specific dashboard:
   - Admin → `/admin/dashboard`
   - Doctor → `/doctor/dashboard`
   - Patient → `/patient/dashboard`
   - User → `/dashboard`

## 📂 File Structure

```
resources/js/Pages/
├── Login.vue                 # Login page
├── Dashboard.vue             # General dashboard
├── Admin/
│   └── Dashboard.vue         # Admin dashboard
├── Doctor/
│   └── Dashboard.vue         # Doctor dashboard
└── Patient/
    └── Dashboard.vue         # Patient dashboard

app/Http/Controllers/
├── LoginController.php
├── DashboardController.php
├── Admin/
│   └── DashboardController.php
├── Doctor/
│   └── DashboardController.php
└── Patient/
    └── DashboardController.php
```

## 🎨 Dashboard Features

### Admin Dashboard
Features:
- Total users, doctors, and patients count
- System status indicator
- User management buttons
- Quick actions (reports, database, logs)
- Recent activities log table
- Dark purple themed design

Components:
- Statistics cards (4 columns)
- User management section
- Quick actions panel
- Activities table

### Doctor Dashboard
Features:
- Patient count (24 in demo)
- Today's appointments (5 in demo)
- Pending medical reports (3 in demo)
- Availability status
- Patient management actions
- Today's schedule with times
- Recent patient activities table
- Green themed design

Components:
- Health statistics cards
- Patient management section
- Today's schedule sidebar
- Recent patient activities table

### Patient Dashboard
Features:
- Blood pressure status
- Heart rate display
- Upcoming appointments count (2 in demo)
- Active prescriptions (3 in demo)
- Book appointment button
- View medical records
- Primary doctor information
- Insurance status
- Upcoming appointments table
- Blue themed design

Components:
- Health summary cards
- My health actions section
- Quick info cards
- Upcoming appointments table

## 🛡️ Authorization & Security

### How Role Protection Works

Routes use the `role` middleware with syntax:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Only admin users can access
});
```

### Accessing Restricted Routes

- **Unauthenticated users**: Redirected to `/login`
- **Wrong role users**: Get 403 Forbidden error
- **Correct role users**: Full access granted

### Role Checking in Code

```php
// In controller
$user = auth()->user();

if ($user->isAdmin()) {
    // Admin logic
} elseif ($user->isDoctor()) {
    // Doctor logic
} elseif ($user->isPatient()) {
    // Patient logic
}
```

## 🧩 Component Structure

### Dashboard Vue Components Include

- **Navigation bar** with user info and logout
- **Statistics cards** showing key metrics
- **Action buttons** for main functions
- **Sidebar panels** for quick access
- **Data tables** for activity/appointment tracking
- **Color-coded status badges**
- **Responsive grid layouts**

### Reactive Features

- Real-time user information
- Logout functionality
- Responsive design (mobile, tablet, desktop)
- Hover effects on interactive elements
- Status indicators and badges

## 🔄 Login Flow with Role-Based Redirect

```
User Login
    ↓
Credentials Validated
    ↓
Check User Role
    ↓
    ├→ Admin → /admin/dashboard
    ├→ Doctor → /doctor/dashboard
    ├→ Patient → /patient/dashboard
    └→ User → /dashboard
```

## 📱 Responsive Design

All dashboards are fully responsive:
- **Mobile**: Single column layout, touch-friendly buttons
- **Tablet**: 2-3 column grid layout
- **Desktop**: Full 4-column stat grid, side-by-side panels

## 🎯 Key URLs to Test

```
Login: http://localhost:8000/login

After Login (based on role):
- Admin:   http://localhost:8000/admin/dashboard
- Doctor:  http://localhost:8000/doctor/dashboard
- Patient: http://localhost:8000/patient/dashboard
- User:    http://localhost:8000/dashboard

Logout: (POST to /logout automatically redirects to login)
```

## 🧪 Testing Each Role

### Test Admin Dashboard
1. Login with `admin@example.com`
2. Should see Admin Dashboard with user management
3. Access `/admin/dashboard` directly - should work
4. Try `/doctor/dashboard` - should get 403 error

### Test Doctor Dashboard
1. Login with `doctor@example.com`
2. Should see Doctor Dashboard with patient info
3. Access `/doctor/dashboard` directly - should work
4. Try `/admin/dashboard` - should get 403 error

### Test Patient Dashboard
1. Login with `patient@example.com`
2. Should see Patient Dashboard with health info
3. Access `/patient/dashboard` directly - should work
4. Try `/admin/dashboard` - should get 403 error

## 🎨 Customization

### Change Dashboard Theme
Edit the Tailwind classes in the Vue component:
```vue
<!-- Change gradient colors -->
<div class="bg-gradient-to-br from-blue-500 to-blue-600">
```

### Add New Statistics
In controller:
```php
return Inertia::render('Admin/Dashboard', [
    'user' => $request->user(),
    'stats' => [
        'newMetric' => 123,
    ],
]);
```

In component:
```vue
<p>{{ stats.newMetric }}</p>
```

### Add New Action Buttons
```vue
<button @click="handleAction" class="button-class">
    Action Label
</button>
```

## 🔧 Troubleshooting

### Dashboard Not Loading
- Verify you're logged in with correct credentials
- Check browser console for errors
- Ensure Vite dev server is running (`npm run dev`)

### Getting 403 Forbidden
- You don't have the required role for that dashboard
- Login with correct credentials for that role
- Check the role in database: `php artisan tinker` → `User::first()->role`

### CSS Not Applying
- Rebuild assets: `npm run build`
- Clear browser cache (Ctrl+Shift+Delete)
- Ensure Tailwind is configured properly

### Routes Not Working
- Clear route cache: `php artisan route:clear`
- Verify routes in `routes/web.php`
- Check middleware aliases in `bootstrap/app.php`

## 📊 Additional Features to Add

1. **Charts & Graphs** - For statistics visualization
2. **Real-time Notifications** - WebSocket updates
3. **File Management** - Upload/download medical records
4. **Messaging System** - Doctor-patient communication
5. **Appointment Booking** - Full scheduling system
6. **Payment Integration** - For consultations
7. **Video Consultation** - Real-time video calls

## 📚 Database Integration

To make dashboards dynamic, update controllers:

```php
// In AdminDashboardController
$stats = [
    'totalUsers' => User::count(),
    'doctors' => User::where('role', 'doctor')->count(),
    'patients' => User::where('role', 'patient')->count(),
];

return Inertia::render('Admin/Dashboard', [
    'user' => $request->user(),
    'stats' => $stats,
]);
```

## 🎓 Next Steps

1. ✅ Test all three dashboards with their respective roles
2. Integrate real data from database
3. Add more features to each dashboard
4. Create additional pages (edit profiles, settings, etc.)
5. Implement notification system
6. Add user activity logging

---

**Your role-based dashboards are ready to use!** 🎉
