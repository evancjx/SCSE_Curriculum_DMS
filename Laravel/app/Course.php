<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "course";

    protected $primaryKey = "code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function prerequisite(){
        return $this->hasMany(Prerequisite::class, 'course_code', 'code')
            ->selectRaw('prerequisiteCode as code');
    }
    public function prerequisiteFor(){
        return $this->hasMany(Prerequisite::class, 'prerequisiteCode', 'code')
            ->selectRaw('course_code as code');
    }
    public function contactHour(){ return $this->hasOne(ContactHour::class); }
    public function objectives(){ return $this->hasOne(Objectives::class); }
    public function learningOutcomes(){ return $this->hasMany(LearningOutcomes::class); }
    public function content(){ return $this->hasMany(Content::class); }
    public function contentAtt(){ return $this->hasOne(ContentAtt::class); }
    public function assessment(){ return $this->hasMany(Assessment::class); }
    public function formativeFeedback(){ return $this->hasOne(FormativeFeedback::class); }
    public function approach(){ return $this->hasMany(Approach::class); }
    public function reference(){ return $this->hasMany(Reference::class); }
    public function instructor(){ return $this->hasMany(Instructor::class); }
    public function schedule(){ return $this->hasMany(Schedule::class); }
    public function appendix(){ return $this->hasMany(Appendix::class); }
}
