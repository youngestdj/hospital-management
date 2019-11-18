<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $attributes = [
      'password' => null,
      'expires' => null,
      'verification_key' => null
    ];
    
    protected $fillable = [
      'email',
      'lastname',
      'firstname',
      'dob',
      'gender',
      'occupation',
      'address',
      'phone',
      'nationality',
      'marital_status',
      'religion',
      'ethnicity',
      'informant'
    ];

    public function history()
    {
        return $this->hasMany('App\Models\History');
    }
}
