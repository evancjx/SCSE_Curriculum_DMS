<?php

namespace App\Http\Controllers;

use App\Course;
use App\GraduateAttributes;
use App\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    public function __construct()
    {
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

    public function index()
    {
        $course = Course::all();
        return view('curriculum.index',[
            'course_list' => $course,
        ]);
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
        dd($data);

        DB::connection('mysql_curriculum')->table('course')->updateOrInsert([
            'code' => $data['course']['code']
        ],[
            'rep' => implode('/', $data['course']['rep']),
            'title' => $data['course']['title'],
            'noAU' => $data['course']['noAU']
        ]);
        if($data['course']['prerequisite'] != 'NIL' || empty($data['course']['prerequisite'])){
            $existing = DB::connection('mysql_curriculum')->table('prerequisite')
                ->where('course_code','=', $data['course']['code'])
                ->select('prerequisiteCode')
                ->get();
            foreach($existing as $exist){
                if(!in_array($exist->prerequisiteCode, explode(',' ,$data['course']['prerequisite']))){
                    DB::connection('mysql_curriculum')->table('prerequisite')
                        ->where('prerequisiteCode', '=', $exist->prerequisiteCode)
                        ->delete();
                }
            }
            foreach(explode(', ', $data['course']['prerequisite']) as $prerequisite){
                DB::connection('mysql_curriculum')->table('prerequisite')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'prerequisiteCode' => $prerequisite
                ]);
            }
        }
        else{
            DB::connection('mysql_curriculum')->table('prerequisite')
                ->where('course_code','=', $data['course']['code'])
                ->delete();
        }
        if(count($data['course']['contactType']) == 3){
            $sql_params = [];
            foreach($data['course']['contactType'] as $key => $contactType){
                $contactType = preg_replace('/\s+/', '', $contactType);
                $sql_params[$contactType] = $data['course']['contactHour'][$key];
            }
            DB::connection('mysql_curriculum')->table('contactHour')->updateOrInsert(['course_code' => $data['course']['code']],$sql_params);
        }

        //Learning Outcomes
        $existing = DB::connection('mysql_curriculum')->table('learningOutcomes')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID - 1), array_keys($data['objectives']['LO']))){
                DB::connection('mysql_curriculum')->table('learningOutcomes')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['objectives']['LO'] as $key => $LO){
            if ($LO == null) continue;
            DB::connection('mysql_curriculum')->table('learningOutcomes')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $key + 1
            ],[
                'description' => $LO
            ]);
            if(!isset($data['mappingLO'][($key+1).'gradAttr'])) continue;
            foreach($data['mappingLO'][($key+1).'gradAttr'] as $gradAttr){
                DB::connection('mysql_curriculum')->table('lo_gradAttr')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'lo_ID' => $key + 1,
                    'gradAttrID' => $gradAttr
                ]);
            }
        }
        //End Learning Outcomes

        if(!empty($data['objectives']['aims'])){
            DB::connection('mysql_curriculum')->table('objectives')->updateOrInsert([
                'course_code' => $data['course']['code']
            ],[
                'courseAims' => $data['objectives']['aims']
            ]);
        }
        if(!empty($data['content']['att1']) && !empty($data['content']['att2'])){
            DB::connection('mysql_curriculum')->table('contentAtt')->updateOrInsert([
                'course_code' => $data['course']['code']
            ],[
                'att1' => $data['content']['att1'],
                'att2' => $data['content']['att2']
            ]);
        }

        //Content
        $existing = DB::connection('mysql_curriculum')->table('content')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID - 1), array_keys($data['content']['topic']))){
                DB::connection('mysql_curriculum')->table('content')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['content']['topic'] as $key => $value){
            if($value == null) continue;
            DB::connection('mysql_curriculum')->table('content')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $data['content']['ID'][$key]
            ],[
                'topics' => $data['content']['topic'][$key]
            ]);
            $temp['details1'] = $data['content']['details1'][$key];
            if ($data['content']['details2'][$key] != null)
                $temp['details2'] = $data['content']['details2'][$key];
            if(isset( $data['content']['merge']) && in_array($key, $data['content']['merge']))
                $temp['rowspan'] = 2;
            else if (isset( $data['content']['merge']) && in_array($key-1, $data['content']['merge']))
                $temp['rowspan'] = 0;
            else
                $temp['rowspan'] = 1;
            DB::connection('mysql_curriculum')->table('contentAttDetails')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'content_ID' => $data['content']['ID'][$key]
            ], $temp);
        }
        //End Content

        //Assessment
        $existing = DB::connection('mysql_curriculum')->table('assessment')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID - 1), array_keys($data['assessment']['component']))){
                DB::connection('mysql_curriculum')->table('assessment')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['assessment']['component'] as $key => $value){
            if($value == null) continue;
            DB::connection('mysql_curriculum')->table('assessment')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $key + 1
            ],[
                'component' => $value,
                'weightage' => $data['assessment']['weight'][$key]
            ]);
            DB::connection('mysql_curriculum')->table('assessment_category')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'assessment_ID' => $key + 1
            ],[
                'category' => $data['assessment'][($key+1).'category']
            ]);
            if(isset($data['assessment'][($key+1).'gradAttr'])){
                foreach($data['assessment'][($key+1).'gradAttr'] as $gradAttr){
                    DB::connection('mysql_curriculum')->table('assessment_gradAttr')->updateOrInsert([
                        'course_code' => $data['course']['code'],
                        'assessment_ID' => $key + 1,
                        'gradAttrID' => $gradAttr
                    ]);
                }
            }
            if(isset($data['assessment'][($key+1).'LO'])){
                foreach($data['assessment'][($key+1).'LO'] as $LO){
                    DB::connection('mysql_curriculum')->table('assessment_lo')->updateOrInsert([
                        'course_code' => $data['course']['code'],
                        'assessment_ID' => $key + 1,
                        'lo_ID' => $LO
                    ]);
                }
            }
            if(!empty($data['assessment']['rubrics'][$key])){
                DB::connection('mysql_curriculum')->table('rubrics')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'assessment_ID' => $key + 1
                ],[
                    'description' => $data['assessment']['rubrics'][$key]
                ]);
            }
        }
        //End Assessment

        if(!empty($data['formativeFeedback'])){
            DB::connection('mysql_curriculum')->table('formativeFeedback')->updateOrInsert([
                'course_code' => $data['course']['code']
            ],[
                'description' => $data['formativeFeedback']
            ]);
        }

        //Approach
        $existing = DB::connection('mysql_curriculum')->table('approach')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID - 1), array_keys($data['approach']['header']))){
                DB::connection('mysql_curriculum')->table('approach')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['approach']['header'] as $key => $header){
            if($header == null) continue;
            DB::connection('mysql_curriculum')->table('approach')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => ($key + 1)
            ],[
                'approach' => $header,
                'description' => $data['approach']['description'][$key]
            ]);
        }
        //End Approach

        //Reference
        $existing = DB::connection('mysql_curriculum')->table('reference')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID - 1), array_keys($data['reference']))){
                DB::connection('mysql_curriculum')->table('reference')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['reference'] as $key => $value){
            if($value == null) continue;
            DB::connection('mysql_curriculum')->table('reference')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => ($key + 1)
            ],[
                'description' => $value
            ]);
        }
        //End Reference

        //Instructor
        $existing = DB::connection('mysql_curriculum')->table('instructor')
            ->where('course_code','=', $data['course']['code'])
            ->select('academicStaffID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->academicStaffID - 1), array_keys($data['instructor']['name']))){
                DB::connection('mysql_curriculum')->table('instructor')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('academicStaffID', '=', $exist->academicStaffID)
                    ->delete();
            }
        }
        foreach($data['instructor']['name'] as $key => $value){
            if($value == null) continue;
            DB::connection('mysql_curriculum')->table('academicStaff')->updateOrInsert([
                'name' => $value,
                'email' => $data['instructor']['email'][$key],
                'office' => $data['instructor']['office'][$key],
                'phone' => $data['instructor']['phone'][$key]
            ]);
            $academicStaffID = DB::connection('mysql_curriculum')->table('academicStaff')
                ->where('name', $value)
                ->where('email', $data['instructor']['email'][$key])
                ->value('ID');
            DB::connection('mysql_curriculum')->table('instructor')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'academicStaffID' => $academicStaffID,
            ]);
        }
        //End Instructor

        //Schedule
        $existing = DB::connection('mysql_curriculum')->table('schedule')
            ->where('course_code','=', $data['course']['code'])
            ->select('weekID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->weekID - 1), array_keys($data['schedule']['topic']))){
                DB::connection('mysql_curriculum')->table('schedule')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('weekID', '=', $exist->weekID)
                    ->delete();
            }
        }
        foreach($data['schedule']['topic'] as $key => $value){
            if($value == null) continue;
            DB::connection('mysql_curriculum')->table('schedule')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'weekID' => $data['schedule']['week'][$key]
            ],[
                'topic' => $data['schedule']['topic'][$key],
                'readings' => $data['schedule']['readings'][$key],
                'activities' => $data['schedule']['activities'][$key]
            ]);
            if(!isset($data['schedule']['LO'][$data['schedule']['week'][$key]])) continue;
            foreach($data['schedule']['LO'][$data['schedule']['week'][$key]] as $LO){
                DB::connection('mysql_curriculum')->table('schedule_lo')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'weekID' => $data['schedule']['week'][$key],
                    'loID' => $LO
                ]);
            }
        }
        //End Schedule

        //Appendix
        $existing = DB::connection('mysql_curriculum')->table('appendix')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID - 1), array_keys($data['appendix']['header']))){
                DB::connection('mysql_curriculum')->table('appendix')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['appendix']['header'] as $key => $header){
            if($header == null) continue;
            DB::connection('mysql_curriculum')->table('appendix')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => ($key+1)
            ],[
                'header' => $header,
                'description' => $data['appendix']['description'][$key]
            ]);
            if(in_array($key+1, array_keys($data['appendix']['criteria']))){
                $criteria = $data['appendix']['criteria'][$key+1];
                foreach($criteria['header'] as $cKey => $cHeader){
                    DB::connection('mysql_curriculum')->table('criteria')->updateOrInsert([
                        'course_code' => $data['course']['code'],
                        'appendixID' => ($key+1),
                        'ID' => ($cKey+1)
                    ],[
                        'header' => $cHeader,
                        'fail' => $criteria['fail'][$cKey],
                        'pass' => $criteria['pass'][$cKey],
                        'high' => $criteria['high'][$cKey]
                    ]);
                }
            }
        }
        //End Appendix
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
