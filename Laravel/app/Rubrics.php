<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Rubrics extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "rubrics";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function assessment(){
        return $this->belongsTo(Assessment::class,
            ['ID','course_code'],
            ['assessment_ID','course_code']
        );
    }
}
