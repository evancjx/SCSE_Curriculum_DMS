<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Assessment_GradAttr extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "assessment_gradattr";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';
}
