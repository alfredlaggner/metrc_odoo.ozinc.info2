<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class MetrcItem extends Model
{
    use Searchable;

    protected $fillable = ['product_id'];

    public function product()
    {
        return $this->hasOne('App\Product','metrc_id','metrc_id');
    }
    public function product1()
    {
        return $this->belongsTo('App\Product','product_id','ext_id');
    }

}
