<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\History;

/** @phan-file-suppress PhanUndeclaredGlobalVariable */
$factory->define(History::class, function () {
    return [
      'patient_id' => 1,
      'doctor_id' => 1,
      'presenting_complaint' => 'Test presenting complaint',
      'presenting_complaint_history' => 'Test presenting complaint history',
      'differential_diagnosis' => 'Test Differential diagnosis',
      'diagnosis' => 'Test diagnosis',
      'prescription' => 'Test prescription',
      'surgical_history' => 'Test surgical history',
      'social_history' => 'Test social history',
      'other_history' => 'Test other history',
      'investigations' => 'Test investigations',
      'treatment_therapy' => 'Test treatment therapy',
      'summary' => 'Test summary'
    ];
});
