<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'metrc_packages';

    protected $fillable = [
        'ext_id',
        'tag',
        'product_id',
        'ref',
        'item',
        'category',
        'item_strain',
        'quantity',
        'uom',
        'lab_testing',
        'date'
    ];
}
