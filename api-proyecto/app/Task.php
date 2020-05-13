<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function section(){
        return $this->belongsTo('App\Section','section_id');
    }

    public function delivers(){
        return $this->hasMany('App\Deliver')->orderBy('id','desc');
    }

    public function tasksfiles(){
        return $this->hasMany('App\Taskfile');
    }

}
