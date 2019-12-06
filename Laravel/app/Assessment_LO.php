<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Assessment_LO extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "assessment_lo";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function assessment(){
        return $this->belongsTo(Assessment::class,
            ['ID', 'course_code'],
            ['assessment_ID', 'course_code']
        );
    }
}
