<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model

{

    protected $table = "salesorders";

    public function order_lines()
    {
        return $this->hasMany('App\SaleInvoice','order_id','salesorder_id');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id', 'ext_id');
    }
    public function stock_picking()
    {
        return $this->hasOne(\App\StockPicking::class, 'salesorder_number',	'sales_order');
    }
    // results in a "problem", se examples below
    public function validation_done() {
        return $this->stock_picking()->where('name','LIKE', '%WH/OUT%');
    }
    public function picking_done() {
        return $this->stock_picking()->where('name','LIKE', '%WH/PICK%');
    }
}
