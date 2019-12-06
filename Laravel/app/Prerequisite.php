<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "prerequisite";

    protected $primaryKey = 'course_code';
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        return $this->belongsTo(Course::class, 'code', 'code');
    }
}
