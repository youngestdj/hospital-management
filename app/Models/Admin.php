<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $attributes = [
      'password' => null,
      'reset_expires' => null,
      'reset_key' => null,
      'verification_key' => null
    ];
    protected $fillable = ['email', 'lastname', 'firstname', 'password'];
}
