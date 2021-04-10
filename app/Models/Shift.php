<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    
    protected $table = 'shifts';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function film()
    {
        return $this->belongsTo('App\Models\Film', 'film_id');
    }

}
