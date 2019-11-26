<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['patient_id', 'doctor_id', 'date', 'description'];

    /**
     * Get the patient associated with the appointment
     */
    public function patient()
    {
        return $this->belongsTo('App\Models\Patient');
    }

    /**
     * Get the doctor associated with the appointment
     */
    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor');
    }
}
