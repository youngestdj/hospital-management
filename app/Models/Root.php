<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Root extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    /** @var array */
    protected $attributes = [
      'firstname' => 'Root',
      'lastname' => 'User',
    ];
}
