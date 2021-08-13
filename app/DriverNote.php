<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverNote extends Model
{
    public function driver_log()
    {
        return $this->belongsTo('App\DriverLog');
    }

	public function comment()
	{
		return $this->hasOne('App\Note','id','note_id');
	}
}