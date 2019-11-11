<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Patient;
use Faker\Generator as Faker;

/** @phan-file-suppress PhanUndeclaredGlobalVariable */
$factory->define(Patient::class, function (Faker $faker) {
    return [
      'email' => $faker->unique()->safeEmail,
      'password' => bcrypt('abcdef'),
      'firstname' => $faker->name(),
      'lastname' => $faker->name(),
      'phone' => $faker->phoneNumber,
      'gender' => 'male',
      'dob' => '21-11-1990',
      'occupation' => 'Teacher',
      'address' => $faker->streetAddress,
      'nationality' => $faker->country,
      'religion' => 'other',
      'ethnicity' => 'other',
      'marital_status' => 'single'
    ];
});
