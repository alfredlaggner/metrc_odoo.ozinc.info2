<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetrcTag extends Model
{

    protected $fillable = ['product_id', 'tag', 'type', 'status', 'commissioned','is_used','used_at'];

}
