<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetrcSourcePacket extends Model
{
    public $fillable = [
        'label', 'source_packet'
    ];

    public function metrc_tag()
    {
//
    }
}
