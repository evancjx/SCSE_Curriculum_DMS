<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objectives extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "objectives";

    protected $primaryKey = "courseCode";
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
