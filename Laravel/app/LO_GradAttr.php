<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class LO_GradAttr extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "lo_gradattr";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function LO(){
        return $this->belongsTo(LearningOutcomes::class,
            ['lo_ID', 'course_code'],
            ['ID', 'course_code']
        );
    }
}
