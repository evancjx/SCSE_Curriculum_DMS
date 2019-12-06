<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class ContentAttDetails extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "contentAttDetails";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function content(){
        return $this->belongsTo(Content::class,
            ['ID',$this->primaryKey],
            ['content_ID','course_code']
        );
    }
}
