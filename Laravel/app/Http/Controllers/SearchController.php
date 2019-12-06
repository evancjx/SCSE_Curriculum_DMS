<?php

namespace App\Http\Controllers;

use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    //
    public function index()
    {
        return view('search.search',[
        ]);
    }
    public function search(Request $data){
//        dd(request());
        $display_result = $searchConditions = $query_result = [];
        if(!empty($data['code']) || !empty($data['title'])){
            array_push($display_result, 'course');

            $condition = "Searching course with ";
            $query = DB::connection('mysql_curriculum')->table('course')
                ->select('rep', 'code', 'title', 'noAU');
            if (!empty($data['code'])){
                $condition.= 'course code of \''.$data['code'].'\'';
                $query->where('code', 'LIKE', '%'.$data['code'].'%');
            }

            if (!empty($data['title'])){
                if(!empty($data['code'])){
                    $condition.= " or ";
                    $query->orwhere('title', 'LIKE', '%'.$data['title'].'%');
                }
                else{
                    $query->where('title', 'LIKE', '%'.$data['title'].'%');
                }
                $condition.= 'course title of \''.$data['title'].'\'';

            }
            array_push($searchConditions, $condition);
            array_push($query_result, $query->get());
        }

        if(!empty($data['prerequisite'])){
            array_push($display_result, 'prerequisite');
            $searchFor = DB::connection('mysql_curriculum')->table('course')
                ->select('rep', 'code', 'title')
                ->where('code', 'LIKE', '%'.$data['prerequisite'].'%')
                ->orwhere('title', 'LIKE', '%'.$data['prerequisite'].'%')
                ->get();
//            dd($searchFor);
            $courses = "";
            if(!empty($searchFor)){
                foreach($searchFor as $key => $course){
                    if($key > 0) $courses.= ", ";
                    $courses.= $course->rep." ".$course->code." ".$course->title;
                }
            }

            $prerequisiteTitle = DB::connection('mysql_curriculum')->table('course')
                ->select('code')
                ->where('title', 'LIKE', '%'.$data['prerequisite'].'%');

            if(isset($data['prerequisiteFor'])){
                $condition = 'Searching \'Pre-requisite for\' course with \''.$data['prerequisite'].'\'';
                if($courses != '') $condition.= '
                    '.$courses.' is pre requisite to the following';

                $prerequisiteCode = DB::connection('mysql_curriculum')->table('prerequisite')
                    ->select('course_code')
                    ->whereIn('prerequisiteCode', $prerequisiteTitle)
                    ->orWhere('prerequisiteCode', '=', $data['prerequisite']);
            }
            else{
                $condition = 'Searching \'Pre requisite of\' course with \''.$data['prerequisite'].'\'';
                if($courses != '') $condition.='
                    the following is/are pre requisite(s) to '.$courses;

                $prerequisiteCode = DB::connection('mysql_curriculum')->table('prerequisite')
                    ->select('prerequisiteCode')
                    ->whereIn('course_code', $prerequisiteTitle)
                    ->orWhere('course_code', '=', $data['prerequisite']);
            }
            $query = DB::connection('mysql_curriculum')->table('course')
                ->select('rep', 'code', 'title', 'noAU')
                ->whereIn('code', $prerequisiteCode);

            array_push($searchConditions, $condition);
            array_push($query_result, $query->get());
        }

        if(!empty($data['instructor'])){
            array_push($display_result, 'instructor');
            $condition  = "Searching for instructor with ".$data['instructor'];

            $academicStaffID = DB::connection('mysql_curriculum')->table('academicStaff')
                ->select('ID')
                ->where('name','LIKE', '%'.$data['instructor'].'%')
                ->orWhere('office','like', '%'.$data['instructor'].'%')
                ->orWhere('phone','like', '%'.$data['instructor'].'%')
                ->orWhere('email','like', '%'.$data['instructor'].'%');
            $course_instructor = DB::connection('mysql_curriculum')->table('course')
                ->join('instructor', 'course.code', '=', 'instructor.course_code')
                ->join('academicStaff', 'instructor.academicStaffID', '=', 'academicStaff.ID')
                ->groupBy('ID', 'name', 'office', 'phone', 'email')
                ->whereIn('instructor.academicStaffID', $academicStaffID)
                ->selectRaw(DB::raw('ID, name, office, phone, email,
                    GROUP_CONCAT(course.rep SEPARATOR \',\') as course,
                    GROUP_CONCAT(course.code SEPARATOR \',\') as code,
                    GROUP_CONCAT(course.title SEPARATOR \',\') as title'));

            array_push($searchConditions, $condition);
            array_push($query_result, $course_instructor->get());
        }

        if(!empty($data['contactType'])){
            array_push($display_result, 'contactType');
            $condition  = "Searching for course with contact type of '";

            $query = DB::connection('mysql_curriculum')->table('course')
                ->join('contactHour', 'course.code', '=', 'contactHour.course_code')
                ->select('rep', 'code', 'title', 'noAU');
            foreach($data['contactType'] as $key => $contactType){
                if(!empty($data['contactHours'][$data['contactType'][$key]]))
                    $value = $data['contactHours'][$data['contactType'][$key]];
                else $value = 0;
                if ($key > 0){
                    $condition.= ", ";
                    $query->orwhere('contactHour.'.$contactType,
                        '>=',
                        $value);
                }
                else
                    $query->where('contactHour.'.$contactType,
                        '>=',
                        $value);
                $condition.=$data['contactType'][$key];
            }
            array_push($searchConditions, $condition."'");
            array_push($query_result, $query->get());
        }

        if(!empty($data['assessment'])){
            array_push($display_result, 'assessment');

            $condition  = "Searching for course with assessment of '".$data['assessment']."'";
            $assessment_code = DB::connection('mysql_curriculum')->table('assessment')
                ->select('course_code')
                ->where('component', 'LIKE', '%'.$data['assessment'].'%');
//            dd($assessment_code->get());
            $query = DB::connection('mysql_curriculum')->table('course')
                ->join('assessment','course.code', '=', 'assessment.course_code')
                ->wherein('course.code', $assessment_code)
                ->groupBy('course.rep', 'course.code', 'title')
                ->orderBy('course.code')
                ->selectRaw('rep, code, title, 
                    GROUP_CONCAT(component SEPARATOR \',\') as component, 
                    GROUP_CONCAT(weightage SEPARATOR \',\') as weightage');
            array_push($searchConditions, $condition);
            array_push($query_result, $query->get());
        }

        $return = [];
        foreach($query_result as $key => $result){
            if($display_result[$key] == 'course' ||
                $display_result[$key] == 'prerequisite' ||
                $display_result[$key] == 'contactType'){
                foreach($result as $course){
                    $course->rep = $course->rep." ".$course->code;
                }
            }
            else if($display_result[$key] == 'instructor'){
                foreach($result as $instructor){
                    $courses = [];
                    foreach(explode(',', $instructor->course) as $cKey => $rep){
                        $courses[$cKey]['rep'] = $rep." ".(explode(',', $instructor->code))[$cKey];
                        $courses[$cKey]['code'] = (explode(',', $instructor->code))[$cKey];
                        $courses[$cKey]['title'] = (explode(',', $instructor->title))[$cKey];
                    }
                    unset($instructor->course);
                    unset($instructor->code);
                    unset($instructor->title);
                    $instructor->courses = $courses;
                }
            }
            else if($display_result[$key] == 'assessment'){
                foreach($result as $course){
                    $course->rep = $course->rep." ".$course->code;
                    $assessment = [];
                    foreach(explode(',', $course->component) as $aKey => $component){
                        $assessment[$aKey]['component'] = $component;
                        $assessment[$aKey]['weightage'] = (explode(',', $course->weightage))[$aKey];
                    }
                    unset($course->component);
                    unset($course->weightage);
                    $course->assessment = $assessment;
                }
            }

            $return[$display_result[$key]]['searchCondition'] = $searchConditions[$key];
            $return[$display_result[$key]]['result'] = $result;
        }

//        dd($return);
        return view('search.search',[
            'data' => $return
        ]);
    }
}
