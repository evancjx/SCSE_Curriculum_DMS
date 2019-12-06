<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicStaff extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "academicStaff";

    protected $primaryKey = "ID";
    public $incrementing = false;
    protected $keyType = 'string';
}
