<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use App\Models\Doctor\Doctor;
use App\Models\Patient\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RoleAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@sakina.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin@123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'doctor@sakina.com'],
            [
                'name' => 'Doctor Demo',
                'password' => Hash::make('doctor@123'),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ]
        );

        $patientUser = User::updateOrCreate(
            ['email' => 'patient@sakina.com'],
            [
                'name' => 'Patient Demo',
                'password' => Hash::make('patient@123'),
                'role' => 'patient',
                'email_verified_at' => now(),
            ]
        );

        Admin::updateOrCreate(
            ['email' => 'admin@sakina.com'],
            [
                'name' => 'System Admin',
                'phone' => '01000000000',
                'position' => 'Super Admin',
                'permissions' => ['all'],
                'created_by' => null,
                'updated_by' => null,
            ]
        );

        Doctor::updateOrCreate(
            ['email' => 'doctor@sakina.com'],
            [
                'name' => 'Doctor Demo',
                'phone' => '01000000001',
                'license_number' => 'DOC-1001',
                'specialization' => 'General Medicine',
                'hospital_name' => 'Sakina Medical Center',
                'verified' => true,
                'bio' => 'Demo doctor account for testing.',
                'years_experience' => 7,
                'rating' => 4.8,
            ]
        );

        $patientData = [
            'name' => 'Patient Demo',
            'phone' => '01000000002',
            'age' => 30,
            'gender' => 'male',
            'city' => 'Cairo',
            'blood_type' => 'O+',
            'address' => 'Demo Street',
            'emergency_contact' => '01000000003',
        ];

        if (Schema::hasColumn('patients', 'user_id')) {
            $patientData['user_id'] = $patientUser->id;
        }
        if (Schema::hasColumn('patients', 'approval_status')) {
            $patientData['approval_status'] = 'approved';
        }
        if (Schema::hasColumn('patients', 'created_by_user_id')) {
            $patientData['created_by_user_id'] = $adminUser->id;
        }
        if (Schema::hasColumn('patients', 'created_by_role')) {
            $patientData['created_by_role'] = 'admin';
        }
        if (Schema::hasColumn('patients', 'approved_by_user_id')) {
            $patientData['approved_by_user_id'] = $adminUser->id;
        }
        if (Schema::hasColumn('patients', 'approved_at')) {
            $patientData['approved_at'] = now();
        }

        Patient::updateOrCreate(
            ['email' => 'patient@sakina.com'],
            $patientData
        );
    }
}

