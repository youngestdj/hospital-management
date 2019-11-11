<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Doctor;
use Faker\Generator as Faker;

/** @phan-file-suppress PhanUndeclaredGlobalVariable */
$factory->define(Doctor::class, function (Faker $faker) {
    return [
      'email' => $faker->unique()->safeEmail,
      'password' => bcrypt('abcdef'),
      'firstname' => $faker->name(),
      'lastname' => $faker->name(),
      'phone' => $faker->phoneNumber,
      'gender' => 'female',
      'dob' => '08-09-1988',
      'specialization' => 'dermatologist'
    ];
});
