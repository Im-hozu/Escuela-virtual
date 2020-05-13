<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public function curse(){
        return $this->belongsTo('App\Curse','curse_id');
    }

    public function recurses(){
        return $this->hasMany('App\Recurse')->orderBy('id','desc');
    }

    public function videos(){
        return $this->hasMany('App\Video')->orderBy('id','desc');
    }

    public function tasks(){
        return $this->hasMany('App\Task')->orderBy('id','desc');
    }
}
