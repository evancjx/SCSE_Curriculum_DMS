<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "common";

    protected $primaryKey = "title";
    public $incrementing = false;
    protected $keyType = 'string';
}
