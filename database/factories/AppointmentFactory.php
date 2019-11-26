<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Appointment;

/** @phan-file-suppress PhanUndeclaredGlobalVariable */
$factory->define(Appointment::class, function () {
    return [
        'patient_id' => 1,
        'date' => '12-12-2032',
        'description' => 'Test appointment description.'
    ];
});
