<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Appendix extends Model
{
    use Compoships;

    protected $connection = 'mysql_curriculum';
    protected $table = "appendix";

    protected $primaryKey = "course_code";
    public $incrementing = false;
    protected $keyType = 'string';

    public function criteria(){
        return $this->hasMany(Criteria::class,
            ['course_code', 'appendixID'],
            ['course_code', 'ID']
        );
    }
}
