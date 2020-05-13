<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curse extends Model
{
    protected $table = 'curses';

    public function sections(){
        return $this->hasMany('App\Section')->orderBy('id','desc');
    }


    public function enrollments(){
        return $this->hasMany('App\Enrollment')->orderBy('curse_id','desc');
    }
}
