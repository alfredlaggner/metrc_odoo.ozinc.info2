<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetrcTest extends Model
{
    protected $fillable = [
        'name', 'is_template', 'json_block','comments', 'result', 'action','test_date','verification',
    ];

    public function metricmain()
    {
        return $this->belongsTo('App\MetricMain');
    }
}
