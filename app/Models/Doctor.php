<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $attributes = [
    'password' => null,
    'expires' => null,
    'verification_key' => null
  ];
    protected $fillable = ['email', 'lastname', 'phone', 'firstname', 'dob', 'gender', 'specialization'];
}
