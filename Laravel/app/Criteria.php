<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "criteria";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';
}
