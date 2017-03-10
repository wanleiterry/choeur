<?php

namespace App\Models;

class Equipment extends BaseModel
{
    protected $table = 'equipment';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $rules = [
        'cabinet_id' => 'required|min:1|integer',
        'name' => 'required|max:60',
        'ip' => 'required',
    ];

    public function cabinet()
    {
        return $this->belongsTo('App\Models\Cabinet', 'cabinet_id', 'id');
    }
}
