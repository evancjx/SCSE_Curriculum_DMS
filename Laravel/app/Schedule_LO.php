<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Schedule_LO extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "schedule_lo";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';
}
