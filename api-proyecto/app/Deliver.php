<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliver extends Model
{
    protected $table = 'delivers';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function task(){
        return $this->belongsTo('App\Task','task_id');
    }
}
