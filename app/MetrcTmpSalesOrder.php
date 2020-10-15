<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetrcTmpSalesOrder extends Model
{
    protected $fillable = ['ext_id','state','activity_state','sales_order','confirmation_date','invoice_status','sales_order_id','create_date','order_date','write_date','amount_untaxed','amount_total','amount_tax','deliver_date','salesperson_id','customer_id'];
    public function order_lines()
    {
        return $this->hasMany('App\SaleInvoice','order_id','salesorder_id');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id', 'ext_id');
    }

}
