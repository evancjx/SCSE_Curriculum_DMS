<?php
namespace App;

use Illuminate\Support\Facades\DB;

class CommonFunc{
    public static function store($data){
        //Main Details
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
            if(!in_array(((int)$exist->ID), array_keys($data['objectives']['LO']))){
                DB::connection('mysql_curriculum')->table('learningOutcomes')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['objectives']['LO'] as $lo_ID => $LO){
            if ($LO == null) continue;
            DB::connection('mysql_curriculum')->table('learningOutcomes')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $lo_ID
            ],[
                'description' => $LO['description']
            ]);
            if(!isset($LO['GradAttr'])) continue;
            foreach($LO['GradAttr'] as $gradAttr){
                DB::connection('mysql_curriculum')->table('lo_gradAttr')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'lo_ID' => $lo_ID,
                    'gradAttrID' => $gradAttr
                ]);
            }
        }
        //End Learning Outcomes

        //Course Aims
        if(!empty($data['objectives']['aims'])){
            DB::connection('mysql_curriculum')->table('objectives')->updateOrInsert([
                'course_code' => $data['course']['code']
            ],[
                'courseAims' => $data['objectives']['aims']
            ]);
        }
        //End Course Aims

        //Content
        if(!empty($data['content']['att1']) && !empty($data['content']['att2'])){
            DB::connection('mysql_curriculum')->table('contentAtt')->updateOrInsert([
                'course_code' => $data['course']['code']
            ],[
                'att1' => $data['content']['att1'],
                'att2' => $data['content']['att2']
            ]);
        }
        //Retrieve existing content ID
        $existing = DB::connection('mysql_curriculum')->table('content')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID), array_keys($data['content']['topic']))){
                DB::connection('mysql_curriculum')->table('content')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['content']['topic'] as $key => $value){
            if(empty($value['ID']) || empty($value['description'])) continue;

            DB::connection('mysql_curriculum')->table('content')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $value['ID']
            ],[
                'topics' => $value['description']
            ]);

            $temp = [];
            if (isset($value['details1']) && $value['details1'] != '')
                $temp['details1'] = $value['details1'];
            if (isset($value['details1']) && $value['details2'] != '')
                $temp['details2'] = $value['details2'];

            if(isset($data['content']['merge']) && in_array($key, $data['content']['merge']))
                $temp['rowspan'] = 2;
            else if (isset( $data['content']['merge']) && in_array($key-1, $data['content']['merge']))
                $temp['rowspan'] = 0;
            else
                $temp['rowspan'] = 1;

            DB::connection('mysql_curriculum')->table('contentAttDetails')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'content_ID' => $value['ID']
            ], $temp);
        }
        //End Content

        //Assessment
        $existing = DB::connection('mysql_curriculum')->table('assessment')
            ->where('course_code','=', $data['course']['code'])
            ->select('ID')
            ->get();
        foreach($existing as $exist){
            if(!in_array(((int)$exist->ID), array_keys($data['assessment']))){
                DB::connection('mysql_curriculum')->table('assessment')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['assessment'] as $assessment_index => $assessment){
//            dd($assessment);
            if($assessment['title'] == '' || $assessment['weight'] == '') continue;
            DB::connection('mysql_curriculum')->table('assessment')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $assessment_index
            ],[
                'component' => $assessment['title'],
                'weightage' => $assessment['weight']
            ]);
            DB::connection('mysql_curriculum')->table('assessment_category')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'assessment_ID' => $assessment_index
            ],[
                'category' => $assessment['category']
            ]);
            if(isset($assessment['gradAttr'])){
                foreach($assessment['gradAttr'] as $assessment_gradAttr){
                    DB::connection('mysql_curriculum')->table('assessment_gradAttr')->updateOrInsert([
                        'course_code' => $data['course']['code'],
                        'assessment_ID' => $assessment_index,
                        'gradAttrID' => $assessment_gradAttr
                    ]);
                }
            }
            if(isset($assessment['LO'])){
                foreach($assessment['LO'] as $assessment_LO){
                    DB::connection('mysql_curriculum')->table('assessment_lo')->updateOrInsert([
                        'course_code' => $data['course']['code'],
                        'assessment_ID' => $assessment_index,
                        'lo_ID' => $assessment_LO
                    ]);
                }
            }
            if(!empty($assessment['rubrics'])){
                DB::connection('mysql_curriculum')->table('rubrics')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'assessment_ID' => $assessment_index
                ],[
                    'description' => $assessment['rubrics']
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
            if(!in_array(((int)$exist->ID), array_keys($data['approach']))){
                DB::connection('mysql_curriculum')->table('approach')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['approach'] as $key => $approach){
            if($approach['header'] == null) continue;
            DB::connection('mysql_curriculum')->table('approach')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $key
            ],[
                'approach' => $approach['header'],
                'description' => $approach['description']
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
            ->where('course_code', '=', $data['course']['code'])
            ->join('academicStaff', 'instructor.academicStaffID', '=', 'academicStaff.ID')
            ->select('academicStaff.ID', 'academicStaff.name', 'academicStaff.email')
            ->get();
        $instructor_email = [];
        foreach($data['instructor'] as $instructor){
            array_push($instructor_email, $instructor['email']);
        }
        foreach($existing as $instructor){
            if(!in_array($instructor->email, $instructor_email)){
                DB::connection('mysql_curriculum')->table('instructor')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('academicStaffID', '=', $instructor->ID)
                    ->delete();
            }
        }
        foreach($data['instructor'] as $key => $instructor){
            if($instructor['name'] == null) continue;
            DB::connection('mysql_curriculum')->table('academicStaff')->updateOrInsert([
                'email' => $instructor['email'],
            ], [
                'name' => $instructor['name'],
                'office' => $instructor['office'],
                'phone' => $instructor['phone']
            ]);
            $academicStaffID = DB::connection('mysql_curriculum')->table('academicStaff')
                ->where('name', $instructor['name'])
                ->where('email', $instructor['email'])
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
            if(!in_array(((int)$exist->weekID), array_keys($data['schedule']))){
                DB::connection('mysql_curriculum')->table('schedule')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('weekID', '=', $exist->weekID)
                    ->delete();
            }
        }
        foreach($data['schedule'] as $schedule_key => $schedule){
            if($schedule['topic'] == null) continue;
            DB::connection('mysql_curriculum')->table('schedule')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'weekID' => $schedule['week']
            ],[
                'topic' => $schedule['topic'],
                'readings' => $schedule['readings'],
                'activities' => $schedule['activities']
            ]);
            if(empty($schedule['LO'])) continue;
            foreach($schedule['LO'] as $schedule_LO){
                DB::connection('mysql_curriculum')->table('schedule_lo')->updateOrInsert([
                    'course_code' => $data['course']['code'],
                    'weekID' => $schedule['week'],
                    'loID' => $schedule_LO
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
            if(!in_array(((int)$exist->ID), array_keys($data['appendix']))){
                DB::connection('mysql_curriculum')->table('appendix')
                    ->where('course_code','=', $data['course']['code'])
                    ->where('ID', '=', $exist->ID)
                    ->delete();
            }
        }
        foreach($data['appendix'] as $appendix_id => $appendix){
            if($appendix['header'] == null) continue;
            DB::connection('mysql_curriculum')->table('appendix')->updateOrInsert([
                'course_code' => $data['course']['code'],
                'ID' => $appendix_id
            ],[
                'header' => $appendix['header'],
                'description' => $appendix['description']
            ]);
            if (isset($appendix['criteria'])){
                foreach($appendix['criteria'] as $criteria_id => $criteria){
                    DB::connection('mysql_curriculum')->table('criteria')->updateOrInsert([
                        'course_code' => $data['course']['code'],
                        'appendixID' => $appendix_id,
                        'ID' => $criteria_id
                    ],[
                        'header' => $criteria['header'],
                        'fail' => $criteria['fail'],
                        'pass' => $criteria['pass'],
                        'high' => $criteria['high']
                    ]);
                }
            }
        }
        //End Appendix
    }
}
