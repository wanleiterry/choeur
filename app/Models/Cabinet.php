<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabinet extends Model
{
    protected $table = 'cabinet';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $dateFormat = 'U';
}
