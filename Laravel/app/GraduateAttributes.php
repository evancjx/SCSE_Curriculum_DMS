<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GraduateAttributes extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "graduateattributes";

    protected $primaryKey = "ID";
    public $incrementing = false;
    protected $keyType = 'string';
}
