<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Content extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "content";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        return $this->belongsTo(Course::class, 'course_code', 'code');
    }

    public function contentAttDetails(){
        return $this->hasOne(ContentAttDetails::class,
            ['content_ID','course_code'],
            ['ID',$this->primaryKey]
        );
    }
}
