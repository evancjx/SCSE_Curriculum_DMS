<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentAtt extends Model
{
    protected $connection = 'mysql_curriculum';
    protected $table = "contentAtt";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function course(){
        $this->belongsTo(Course::class);
    }
}
