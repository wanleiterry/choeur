<?php

namespace App\Models;

class Pending extends BaseModel
{
    protected $table = 'pending';

    protected $guarded = ['id'];

    public $timestamps = true;
}
