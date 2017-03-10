<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'user';

    protected $guarded = ['id'];

    protected $hidden = ['password'];

    public $timestamps = true;
}
