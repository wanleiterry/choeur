<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pending extends Model
{
    protected $table = 'pending';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $dateFormat = 'U';
}
