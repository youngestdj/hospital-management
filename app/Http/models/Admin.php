<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $attributes = [
      'password' => null,
      'expires' => null,
      'verification_key' => null
    ];
    protected $fillable = ['email', 'lastname', 'firstname'];
}
