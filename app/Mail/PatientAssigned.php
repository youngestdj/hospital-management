<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PatientAssigned extends Mailable
{
    use Queueable, SerializesModels;
    public $patient;
    public $doctor;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($patient, $doctor, $date)
    {
        $this->patient = $patient;
        $this->doctor = $doctor;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('patientAssigned');
    }
}
