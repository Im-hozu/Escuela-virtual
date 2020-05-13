<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function tasksfiles(){
        return $this->hasMany('App\Taskfile');
    }
}
