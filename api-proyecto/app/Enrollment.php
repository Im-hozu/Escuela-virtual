<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'enrollments';
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function curse(){
        return $this->belongsTo('App\Curse','curse_id');
    }

}
