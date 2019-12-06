<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approach extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "approach";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
