<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "instructor";

    protected $primaryKey = "academicStaffID";
    public $incrementing = false;
    protected $keyType = 'string';

    public function academicStaff(){
        return $this->hasOne(AcademicStaff::class, 'ID', 'academicStaffID');
    }
}
