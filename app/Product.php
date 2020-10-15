<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Searchable;
    public function item()
    {
        return $this->hasOne('App\MetrcItem','metrc_id','metrc_id');
    }
    public function item1()
    {
        return $this->belongsTo('App\MetrcItem','ext_id', 'product_id');
    }
}
