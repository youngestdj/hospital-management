<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Admin;
use Faker\Generator as Faker;

/** @phan-file-suppress PhanUndeclaredGlobalVariable */
$factory->define(Admin::class, function (Faker $faker) {
    return [
      'email' => $faker->unique()->safeEmail,
      'password' => bcrypt('abcdef'),
      'firstname' => $faker->name(),
      'lastname' => $faker->name()
    ];
});
