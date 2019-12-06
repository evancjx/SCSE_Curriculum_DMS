<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "schedule";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function LO(){
        return $this->hasMany(Schedule_LO::class,
            ['course_code', 'weekID'],
            ['course_code', 'weekID']
        );
    }
}
