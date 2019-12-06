<?php

namespace App\Exports;

use App\ContactHour;
use App\Course;
use App\Prerequisite;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CurriculumExport implements FromQuery
{
    use Exportable;

    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function query()
    {
//        $course = Course::query()->where('code', '=', $this->code);
        $course = DB::connection('mysql_curriculum')->table('course')
            ->where('code', '=', $this->code)
            ->first();
        return $course;
    }
//    public function map($data): array
//    {
//        $mapping = [];
//        //Main details
//        array_push($mapping, array('Main Details'));
//        array_push($mapping, array('rep', $data->rep));
//        array_push($mapping, array('code', $data->code));
//        array_push($mapping, array('title', $data->title));
//        array_push($mapping, array('noAU', $data->noAU));
//        array_push($mapping, array('category'));
//        array_push($mapping, array('proposalDate'));
//        array_push($mapping, array());
//
//        //Prerequisite
//        $query = Prerequisite::query()->where('course_code', '=', $this->code)->get();
//        array_push($mapping, array('Pre-requisite'));
//        foreach($query as $prerequisite){
//            array_push($mapping, array($prerequisite->prerequisiteCode));
//        }
//        array_push($mapping, array());
//
//        //Contact Hour
//        $query = DB::connection('mysql_curriculum')->table('contactHour')
//            ->where('course_code', '=', $this->code)
//            ->select('lecture', 'tel', 'tutorial', 'lab', 'exampleclass')
//            ->first();
//        array_push($mapping, array('Contact Hour'));
//        array_push($mapping, array('Lecture', 'TEL', 'Tutorial', 'Lab', 'ExampleClass'));
//        array_push($mapping, [$query->lecture, $query->tel, $query->tutorial, $query->lab, $query->exampleclass]);
//        array_push($mapping, array());
//
//        //Course Aim
//        $query = DB::connection('mysql_curriculum')->table('objectives')
//            ->where('course_code', '=', $this->code)
//            ->first();
//        array_push($mapping, array('Course Aims'));
//        array_push($mapping, array(htmlentities(html_entity_decode($query->courseAims))));
//        array_push($mapping, array());
//
////        dd($mapping);
//        return $mapping;
//    }
}
