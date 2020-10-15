<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetrcPlannedRoute extends Model
{
    protected $fillable = ['customer_id', 'planned_route','driver_id','vehicle_id'];

    public function planned_route()
    {
        return $this->belongsTo('App\SaleInvoice');
    }
}
