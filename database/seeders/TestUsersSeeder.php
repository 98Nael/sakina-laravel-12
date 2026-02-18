<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sakina.com',
            'password' => Hash::make('admin@123'),
            'role' => 'admin',
        ]);

        // Create Doctor
        User::create([
            'name' => 'Doctor User',
            'email' => 'doctor@sakina.com',
            'password' => Hash::make('doctor@123'),
            'role' => 'doctor',
        ]);

        // Create Patient
        User::create([
            'name' => 'Patient User',
            'email' => 'patient@sakina.com',
            'password' => Hash::make('patient@123'),
            'role' => 'patient',
        ]);

        echo "\n✅ Test users created successfully!\n";
    }
}
