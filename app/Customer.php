<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $fillable = ['ext_id','ext_id_contact','license','name','contact','street','street2','city','zip'];

    public function contact()
    {
        return $this->hasOne('App\Contact','ext_id','ext_id');
    }

}
//id	child_ids/id	email	name	street	street2	city	zip	phone
