<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class MetrcPackage extends Model
{
    use Searchable;

    protected $searchable = [
        'tag',
        'source_tag',
    ];

    protected $table = 'metrc_packages';
    public $fillable = [
        'source_tag',
    ];
}
