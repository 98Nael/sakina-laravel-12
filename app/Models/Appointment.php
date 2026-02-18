<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $table = 'appointments';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date_time',
        'status',
        'reason',
        'notes',
        'duration_minutes',
        'reminder_sent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
            'reminder_sent' => 'boolean',
        ];
    }

    /**
     * Get the patient of this appointment.
     */
    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient\Patient::class, 'patient_id');
    }

    /**
     * Get the doctor of this appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(\App\Models\Doctor\Doctor::class, 'doctor_id');
    }

    /**
     * Get all prescriptions created in this appointment.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'appointment_id');
    }
}
