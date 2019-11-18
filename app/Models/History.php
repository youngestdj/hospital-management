<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $fillable = [
      'patient_id',
      'doctor_id',
      'presenting_complaint',
      'presenting_complaint_history',
      'differential_diagnosis',
      'diagnosis',
      'prescription',
      'surgical_history',
      'social_history',
      'other_history',
      'investigations',
      'treatment_therapy',
      'summary'
    ];

    /**
     * Get the patient that owns the history
     */
    public function patient()
    {
        return $this->belongsTo('App\Models\Patient');
    }

    /**
     * Get the doctor that recorded the history
     */
    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor');
    }
}
