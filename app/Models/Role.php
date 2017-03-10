<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $table = 'role';

    protected $guarded = ['id'];

    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }
}
