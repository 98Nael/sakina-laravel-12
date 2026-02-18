# User Authentication & Role System Documentation

## Overview
This Laravel 12 application now includes a comprehensive user authentication system with 4 user roles:
- **admin** - Administrator with full access
- **user** - Regular user
- **doctor** - Doctor role for healthcare features
- **patient** - Patient role for healthcare features

## Getting Started

### 1. Run Migration
First, run the migration to add the `role` column to the users table:

```bash
php artisan migrate
```

### 2. Seed Test Data
Seed the database with test users of different roles:

```bash
php artisan db:seed
```

This creates:
- Admin: admin@example.com
- Doctor: doctor@example.com
- Patient: patient@example.com
- Regular User: test@example.com

All test users have password: `password`

## Using Roles in Your Application

### Check User Role in Code

```php
// Check if user is an admin
if (auth()->user()->isAdmin()) {
    // Admin-only logic
}

// Check if user is a doctor
if (auth()->user()->isDoctor()) {
    // Doctor-only logic
}

// Check if user is a patient
if (auth()->user()->isPatient()) {
    // Patient-only logic
}

// Check by role name
if (auth()->user()->hasRole('doctor')) {
    // Do something
}

// Check if user has any of multiple roles
if (auth()->user()->hasAnyRole(['admin', 'doctor'])) {
    // Admin or doctor logic
}
```

### Using Middleware for Route Protection

Register middleware in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

Then protect your routes:

```php
// Admin-only route
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Multiple roles allowed
Route::middleware('role:admin,doctor')->group(function () {
    Route::get('/medical/access', [MedicalController::class, 'access']);
});
```

### Using Roles in Blade Templates

```blade
@if(auth()->user()->isAdmin())
    <div>Admin Tools</div>
@elseif(auth()->user()->isDoctor())
    <div>Doctor Portal</div>
@elseif(auth()->user()->isPatient())
    <div>Patient Portal</div>
@endif
```

## User Model Methods

The `User` model includes the following role-related methods:

| Method | Purpose |
|--------|---------|
| `isAdmin()` | Check if user is admin |
| `isDoctor()` | Check if user is doctor |
| `isPatient()` | Check if user is patient |
| `hasRole($role)` | Check if user has specific role |
| `hasAnyRole($roles)` | Check if user has any role in array |

## Database Schema

The users table now includes:

```
- id (primary key)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- role (enum: admin, user, doctor, patient) - default: user
- remember_token (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Creating Users Programmatically

```php
// Create a doctor
$doctor = User::factory()->doctor()->create([
    'name' => 'Dr. Jane Smith',
    'email' => 'jane@hospital.com',
]);

// Create a patient
$patient = User::factory()->patient()->create([
    'name' => 'John Patient',
    'email' => 'john@patient.com',
]);

// Create an admin
$admin = User::factory()->admin()->create([
    'name' => 'Admin User',
    'email' => 'admin@app.com',
]);

// Manually set role
$user = User::create([
    'name' => 'New User',
    'email' => 'newuser@example.com',
    'password' => Hash::make('password'),
    'role' => 'user',
]);
```

## File Structure

- `app/Models/User.php` - Updated with role methods
- `app/Http/Middleware/CheckRole.php` - Middleware for checking multiple roles
- `app/Http/Middleware/IsAdmin.php` - Middleware for admin-only routes
- `database/migrations/2025_02_11_000003_add_role_to_users_table.php` - Migration to add role column
- `database/factories/UserFactory.php` - Updated with role states
- `database/seeders/DatabaseSeeder.php` - Updated to create test users

## Next Steps

1. Register the middleware in your `bootstrap/app.php` file
2. Create controllers and routes for your doctor and patient portals
3. Implement Blade templates for different user roles
4. Add authorization policies if needed for more complex permission checks

