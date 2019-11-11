<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


/** @phan-file-suppress PhanUndeclaredGlobalVariable */
$factory->define(App\Models\Root::class, function () {
    return [
      'email' => \config('mail.root'),
      'password' => bcrypt('abcdef'),
      'firstname' => 'Root',
      'lastname' => 'User'
    ];
});
