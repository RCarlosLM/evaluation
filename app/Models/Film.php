<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    
    protected $table = 'films';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function shifts()
    {
        return $this->hasMany('App\Models\Shift');
    }

}
