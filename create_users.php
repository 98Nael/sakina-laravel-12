<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

try {
    // Create Admin
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@sakina.com',
        'password' => Hash::make('admin@123'),
        'role' => 'admin',
    ]);

    // Create Doctor
    $doctor = User::create([
        'name' => 'Doctor User',
        'email' => 'doctor@sakina.com',
        'password' => Hash::make('doctor@123'),
        'role' => 'doctor',
    ]);

    // Create Patient
    $patient = User::create([
        'name' => 'Patient User',
        'email' => 'patient@sakina.com',
        'password' => Hash::make('patient@123'),
        'role' => 'patient',
    ]);

    echo "✅ Admin: admin@sakina.com / admin@123\n";
    echo "✅ Doctor: doctor@sakina.com / doctor@123\n";
    echo "✅ Patient: patient@sakina.com / patient@123\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
