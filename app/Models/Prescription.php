<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory;

    protected $table = 'prescriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
        'refills_allowed',
        'refills_used',
        'status',
        'issued_date',
        'expiry_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_date' => 'datetime',
            'expiry_date' => 'datetime',
        ];
    }

    /**
     * Get the patient of this prescription.
     */
    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient\Patient::class, 'patient_id');
    }

    /**
     * Get the doctor of this prescription.
     */
    public function doctor()
    {
        return $this->belongsTo(\App\Models\Doctor\Doctor::class, 'doctor_id');
    }

    /**
     * Get the appointment of this prescription.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
