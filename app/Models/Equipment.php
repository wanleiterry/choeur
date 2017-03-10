<?php

namespace App\Models;

class Equipment extends BaseModel
{
    protected $table = 'equipment';

    protected $guarded = ['id'];

    public $timestamps = true;
}
