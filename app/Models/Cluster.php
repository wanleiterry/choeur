<?php

namespace App\Models;

class Cluster extends BaseModel
{
    protected $table = 'cluster';

    protected $guarded = ['id'];

    public $timestamps = true;

    // protected $dateFormat = 'U';

    protected $rules = [
        'parent_id' => 'required|min:0|integer',
        'name' => 'required|max:60',
        'ip' => 'required',
    ];
}
