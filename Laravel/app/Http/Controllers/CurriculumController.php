<?php

namespace App\Http\Controllers;

use App\Course;
use App\GraduateAttributes;
use App\Common;
use App\CommonFunc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function courseHeader($course){
        $courseCodeSplit = explode("/", $course->rep);
        $courseCodes = "";
        foreach($courseCodeSplit as $key => $item){
            if($key == 1) $courseCodes.= " and ";
            $courseCodes.= $item.$course->code;
        }
        $course['codeSplit'] = $courseCodeSplit;
        $course['header'] = $courseCodes." − ".$course->title;

        return $course;
    }

    public function index(){
        $course = Course::all();
        return view('curriculum.index', ['course_list' => $course]);
    }

    public function create(){
        $grad_attr = GraduateAttributes::all();
        return view('curriculum.curriculum_form', [
            'title' => 'Insert Curriculum',
            'func' => 'Insert',
            'grad_attr' => $grad_attr,
        ]);
    }

    public function store(Request $data){
        //dd($data);
        CommonFunc::store($data);

        return redirect('/curriculum/'.$data['course']['code']);
    }

    public function edit($code){
        $course = Course::findOrFail($code);
        $course = $this->courseHeader($course);

        $contactHour = json_decode($course->contactHour, true) ;
        unset($contactHour['course_code']);

        foreach($course->learningOutcomes as $learningOutcome){
            $lo_gradAttr = [];
            foreach($learningOutcome->grad_attr as $gradAttr){
                array_push($lo_gradAttr, $gradAttr->gradAttrID);
            }
            $learningOutcome['gradAttr'] = $lo_gradAttr;
            unset($learningOutcome['grad_attr']);
        }

        foreach($course->assessment as $assessment){
            $LOs = [];
            foreach($assessment->assessment_lo as $lo_tested){
                array_push($LOs, $lo_tested->lo_ID);
            }
            $assessment['LOs'] = $LOs;
            unset($assessment['assessment_lo']);

            $assessment_gradAttr = [];
            foreach($assessment->assessment_gradAttr as $grad_attr){
                array_push($assessment_gradAttr, $grad_attr->gradAttrID);
            }
            $assessment['gradAttr'] = $assessment_gradAttr;
            unset($assessment['assessment_gradAttr']);

            $assessment['category'] = $assessment->assessment_category->category;
            unset($assessment['assessment_category']);
        }

        foreach($course->schedule as $schedule){
            $schedule_lo = [];
            foreach($schedule->LO as $LO){
                array_push($schedule_lo, $LO->loID);
            }
            $schedule['LO'] = $schedule_lo;
            unset($schedule['l_o']);
        }
        $grad_attr = GraduateAttributes::all();
        return view('curriculum.curriculum_form', [
            'title' => 'Update '.$course->rep.' '.$course->code,
            'func' => 'Update',
            'course' => $course,
            'contactHour' => $contactHour,
            'grad_attr' => $grad_attr,
        ]);
    }

    public function show($code){
        $course = Course::findOrFail($code);
        $grad_attr = GraduateAttributes::all();
        $common = Common::all();

        $course = $this->courseHeader($course);

        $contactHour = json_decode($course->contactHour, true) ;
        unset($contactHour['course_code']);
        $grad_attr_count_list = [] ;
        foreach($grad_attr as $GA){ $grad_attr_count_list[$GA->ID] = 0; }
        if(!$course->learningOutcomes->isEmpty()){
            foreach($course->learningOutcomes as $LO){
                foreach($LO->grad_attr as $lo_gradAttr){
                    $grad_attr_count_list[$lo_gradAttr->gradAttrID]++;
                }
            }
            foreach($grad_attr_count_list as $ID => $value){
                $grad_attr_count_list[$ID] = $grad_attr_count_list[$ID]/$course->learningOutcomes->count();
            }
        }

        return view('curriculum.curriculum',[
            'course' => $course,
            'contactHour' => $contactHour,
            'grad_attr' => $grad_attr,
            'lo_ga_count' => $grad_attr_count_list,
            'common' => $common,
        ]);
    }
}
