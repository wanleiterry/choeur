<?php

namespace App\Models;

class Cluster extends BaseModel
{
    protected $table = 'cluster';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $rules = [
        'parent_id' => 'required|min:0|integer',
        'name' => 'required|max:60',
        'ip' => 'required',
    ];

    public function dot()
    {
        return $this->hasMany(get_class($this), 'parent_id', 'id');
    }

    public function cluster()
    {
        return $this->hasOne(get_class($this), 'id', 'parent_id');
    }
}
