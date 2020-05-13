<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasksfile extends Model
{
    protected $table = 'tasksfiles';

    public function file(){
        return $this->belongsTo('App\File','file_id');
    }

    public function task(){
        return $this->belongsTo('App\Task','task_id');
    }
}
