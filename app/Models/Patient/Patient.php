<?php

namespace App\Models\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $table = 'patients';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'age',
        'gender',
        'blood_type',
        'address',
        'city',
        'photo_path',
        'medical_history',
        'allergies',
        'emergency_contact',
        'approval_status',
        'created_by_user_id',
        'created_by_role',
        'approved_by_user_id',
        'approved_at',
        'approval_notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'medical_history' => 'json',
            'allergies' => 'json',
        ];
    }

    /**
     * Get all doctors assigned to this patient.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'patient_doctors', 'patient_id', 'doctor_id');
    }

    /**
     * Get all appointments for this patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Get all prescriptions for this patient.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * Get all medical records for this patient.
     */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id');
    }
}
