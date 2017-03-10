<?php

namespace App\Models;

class Cabinet extends BaseModel
{
    protected $table = 'cabinet';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $rules = [
        'dot_id' => 'required|min:1|integer',
        'name' => 'required|max:60',
        'ip' => 'required',
    ];

    public function dot()
    {
        return $this->belongsTo('App\Models\Cluster', 'dot_id', 'id');
    }

    public function equipment()
    {
        return $this->hasMany('App\Models\Equipment', 'cabinet_id', 'id');
    }
}
