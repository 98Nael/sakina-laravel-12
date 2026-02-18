<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalRecordFactory> */
    use HasFactory;

    protected $table = 'medical_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'diagnosis',
        'treatment',
        'symptoms',
        'test_results',
        'record_date',
        'notes',
        'is_confidential',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'test_results' => 'json',
            'symptoms' => 'json',
            'record_date' => 'datetime',
            'is_confidential' => 'boolean',
        ];
    }

    /**
     * Get the patient of this medical record.
     */
    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient\Patient::class, 'patient_id');
    }

    /**
     * Get the doctor of this medical record.
     */
    public function doctor()
    {
        return $this->belongsTo(\App\Models\Doctor\Doctor::class, 'doctor_id');
    }
}
