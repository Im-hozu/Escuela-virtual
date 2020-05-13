<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recurse extends Model
{
    protected $table = 'recurses';

    public function file(){
        return $this->belongsTo('App\File','file_id');
    }

    public function section(){
        return $this->belongsTo('App\Section','section_id');
    }
}
