<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function comments(){
        return $this->hasMany('App\Comment')->orderBy('id','desc');
    }

    public function section(){
        return $this->belongsTo('App\Section','section_id');
    }
}
