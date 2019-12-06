<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Assessment extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "assessment";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function assessment_lo(){
        return $this->hasMany(Assessment_LO::class, ['assessment_ID','course_code'], ['ID','course_code'] );
    }
    public function assessment_gradAttr(){
        return $this->hasMany(Assessment_GradAttr::class, ['assessment_ID','course_code'], ['ID','course_code']);
    }
    public function assessment_category(){
        return $this->hasOne(Assessment_Category::class, ['assessment_ID','course_code'], ['ID','course_code'] );
    }
    public function rubrics(){
        return $this->hasOne(Rubrics::class, ['assessment_ID','course_code'], ['ID','course_code'] );
    }
}
