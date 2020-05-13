<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    public function sender(){
        return $this->belongsTo('App\User','sender');
    }

    public function addressee(){
        return $this->belongsTo('App\User','addressee');
    }
}
