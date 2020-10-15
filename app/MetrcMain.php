<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetrcMain extends Model
{
    protected $fillable = [
        'name',
    ];

    public function metrictest()
    {
        return $this->hasMany('App\MetricTest');
    }

}
