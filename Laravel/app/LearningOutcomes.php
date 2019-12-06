<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class LearningOutcomes extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "learningOutcomes";

    protected $primaryKey = 'courseCode';
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        return $this->belongsTo(Course::class );
    }
    public function grad_attr(){
        return $this->hasMany(LO_GradAttr::Class,
            ['lo_ID','course_code'],
            ['ID','course_code']
        );
    }
}
