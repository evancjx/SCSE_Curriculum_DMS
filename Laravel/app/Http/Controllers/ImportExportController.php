<?php

namespace App\Http\Controllers;

use App\CommonFunc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function to_csv($curriculum){
        $filename = str_replace('/', '_', $curriculum['Main Details']['rep'])." ".$curriculum['Main Details']['code']." - ".$curriculum['Main Details']['title'].".csv";
        $filename  = '[Laravel] '.$filename;
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        ob_end_clean();
        $fp = fopen('php://output', 'w');

        foreach($curriculum as $header => $details){
            fputcsv($fp, array($header));
            switch ($header){
                case 'Main Details':
                    foreach($details as $detail_header=>$value){
                        fputcsv($fp, array($detail_header, $value));
                    }
                    break;
                case 'Pre-requisite':
                    if($details == null) break;
                    foreach($details as $value){
                        fputcsv($fp, [$value]);
                    }
                    break;
                case 'Contact Hour':
                    $ch_header = [];
                    $ch_details = [];
                    foreach($details as $detail_header => $value){
                        $ch_header[]=$detail_header;
                        $ch_details[]=$value;
                    }
                    fputcsv($fp, $ch_header);
                    fputcsv($fp, $ch_details);
                    break;
                case 'Course Aims':
                case 'Formative Feedback':
                    if($details == null) break;
                    fputcsv($fp, array($details));
                    break;
                case 'Learning Outcomes':
                    fputcsv($fp, ['Description', null, null, null, 'Grad Attributes']); // Header
                    foreach($details as $key => $detail){
                        $row = [
                            $detail['description'],
                            null,
                            null,
                            null,
                            $detail['gradAttr']
                        ];
                        fputcsv($fp, $row);
                    }
                    break;
                case 'Content Att':
                    fputcsv($fp, ['Att 1', 'Att 2']); // Header
                    if($details == null) break;
                    fputcsv($fp, [$details['att1'], $details['att2']]);
                    break;
                case 'Content':
                    fputcsv($fp, ['S/N', 'Topic', '[Att 1]', '[Att 2]', 'Row span']); // Header
                    foreach($details as $key => $detail){
                        fputcsv($fp, [$key, $detail['Topic'], $detail['details1'], $detail['details2'], $detail['rowspan']]);
                    }
                    break;
                case 'Assessment':
                    fputcsv($fp, ['S/N', 'Component', 'Weightage', 'Category', 'LOs', 'Grad Attributes', 'Rubrics']); // Header
                    foreach($details as $key => $detail){
                        $row = [$key, $detail['component'], $detail['weightage'], $detail['category']];
                        if(isset($detail['LOs']))
                            $row[] = $detail['LOs'];
                        if(isset($detail['gradAttr']))
                            $row[] = $detail['gradAttr'];
                        $row[] = $detail['rubrics'];
                        fputcsv($fp, $row);
                    }
                    break;
                case 'Approaches':
                    fputcsv($fp, ['Header', 'Description']); // Header
                    if($details == null) break;
                    foreach($details as $key => $detail)
                        fputcsv($fp, [$detail['approach'], $detail['description']]);
                    break;
                case 'References':
                    if($details == null) break;
                    foreach($details as $key => $detail){
                        fputcsv($fp, [$detail['description']]);
                    }
                    break;
                case 'Instructors':
                    fputcsv($fp, ['Name', 'Office', 'Phone', 'Email']); // Header
                    if($details == null) break;
                    foreach($details as $key => $detail)
                        fputcsv($fp, [$detail['name'], $detail['office'], $detail['phone'], $detail['email']]);
                    break;
                case 'Schedule':
                    fputcsv($fp, ['Week', 'Topic', 'Readings', 'Activities', 'LOs']); // Header
                    if($details == null) break;
                    foreach($details as $key => $detail)
                        fputcsv($fp, [$key, $detail['topic'], $detail['readings'], $detail['activities'], $detail['LOs']]);
                    break;
                case 'Appendix':
                    if($details == null) {
                        foreach(['ID', 'Header', 'Description', '---Criteria---', 'Header', 'Fail', 'Pass', 'High'] as $header)
                            fputcsv($fp, [$header]);

                        break;
                    }
                    foreach($details as $key => $appendix){
                        fputcsv($fp, ['ID', $key]);
                        fputcsv($fp, ['Header', $appendix['header']]);
                        fputcsv($fp, ['Description', $appendix['description']]);
                        fputcsv($fp, ['---Criteria---']); // Header
                        if(isset($appendix['Criteria'])){
                            //fputcsv($fp, ['Appendix ID', $key]);
                            foreach($appendix['Criteria'] as $row){
                                foreach($row as $cHeader => $criterion)
                                    fputcsv($fp, [ucfirst($cHeader), $criterion]);
                            }
                        }
                        fputcsv($fp, [null, null]);
                    }
            }
            fputcsv($fp, array(null, null, null, null, null, 'Separator [keep this row between each sections]'));
        }
    }

    public function index(){
        return view('import.index',[]);
    }

    public function import(Request $request){
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        $valid_extension = array('csv');
        $maxFileSize = 2097152;

        if (!in_array(strtolower($extension), $valid_extension))
            exit(403);
        else if ($fileSize > $maxFileSize)
            exit(403);

//        dd(fgetcsv(, 10000, ','));
        $file = fopen($tempPath, 'r');
        $sections = [
            'Main Details', 'Pre-requisite', 'Contact Hour', 'Course Aims', 'Learning Outcomes',
            'Content Att', 'Content', 'Assessment',
            'Formative Feedback', 'Approaches', 'References', 'Instructors',
            'Schedule', 'Appendix', 'Criteria'];
        $curriculum = array(
            'course'=>array(
                'code'=>'',
                'rep'=>'',
                'title'=>'',
                'prerequisite'=>'NIL',
                'noAU'=>'',
                'contactType'=>array(),
                'contactHour'=>array()
                ),
            'objectives'=>array(
                'aims'=>'',
                'LO'=>array()
            ),
            'content'=>array(
                'att1'=>'',
                'att2'=>'',
                'topic'=>array(),
                'merge'=>array()),
            'assessment'=>[],
            'formativeFeedback'=>'',
            'approach'=>[],
            'reference'=>[],
            'instructor'=>[],
            'schedule'=>[],
            'appendix'=>[],
        );
        $currentAppendixID = 0; $currentCriteriaID = 0;
        $isAppendix = True;
        $currentSection = null;
        while(($columns = fgetcsv($file, 1000, ',')) !== false){
            if (in_array($columns[0], $sections)){
                $currentSection = $columns[0];
                continue;
            }
            switch ($currentSection) {
                case 'Main Details':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'rep' and !empty($columns[1]))
                        $curriculum['course']['rep'] = explode('/', $columns[1]);
                    else if (isset($columns[1]))
                        $curriculum['course'][$columns[0]] = $columns[1];
                    else
                        $curriculum['course'][$columns[0]] = '';
                    break;
                case 'Pre-requisite':
                    if($columns[0] == null) break;
                    array_push($curriculum['prerequisite'], $columns[0]);
                    break;
                case 'Contact Hour':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'Lecture') break;

                    if (isset($columns[0]) and $columns[0] != ''){
                        array_push($curriculum['course']['contactType'], 'lecture');
                        array_push($curriculum['course']['contactHour'], $columns[0]);
                    }
                    if (isset($columns[1]) and $columns[1] != ''){
                        array_push($curriculum['course']['contactType'], 'tel');
                        array_push($curriculum['course']['contactHour'], $columns[1]);
                    }
                    if (isset($columns[2]) and $columns[2] != ''){
                        array_push($curriculum['course']['contactType'], 'tutorial');
                        array_push($curriculum['course']['contactHour'], $columns[2]);
                    }
                    if (isset($columns[3]) and $columns[3] != ''){
                        array_push($curriculum['course']['contactType'], 'lab');
                        array_push($curriculum['course']['contactHour'], $columns[3]);
                    }
                    if (isset($columns[4]) and $columns[4] != ''){
                        array_push($curriculum['course']['contactType'], 'exampleclass');
                        array_push($curriculum['course']['contactHour'], $columns[4]);
                    }
                    break;
                case 'Course Aims':
                    if ($columns[0] == null) break;
                    $curriculum['objectives']['aims'] = $columns[0];
                    break;
                case 'Learning Outcomes':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'Description')break;

                    if (isset($columns[0])){
                        $index = sizeof($curriculum['objectives']['LO']) + 1;
                        $curriculum['objectives']['LO'][$index]['description'] = $columns[0];
                        if (isset($columns[4]) and !empty($columns)){
                            $curriculum['objectives']['LO'][$index]['GradAttr'] = explode(',', preg_replace('/\s/', '', $columns[4]));
                        }
                    }
                    break;
                case 'Content Att':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'Att 1')break;

                    $curriculum['content']['att1'] = $columns[0];
                    $curriculum['content']['att2'] = $columns[1];
                    break;
                case 'Content':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'S/N')break;

                    if (isset($columns[0])){
                        $curriculum['content']['topic'][$columns[0]] = [
                            'ID'=>$columns[0],
                            'description'=>$columns[1],
                            'details1'=>$columns[2],
                            'details2'=>$columns[3]
                        ];
                        if (isset($columns[4]) && $columns[4] == '2')
                            array_push($curriculum['content']['merge'], $columns[0]);
                    }
                    break;
                case 'Assessment':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'S/N') break;

                    if (isset($columns[0])){
                        $curriculum['assessment'][$columns[0]] = [
                            'title'=>$columns[1],
                            'LO'=>explode(', ',$columns[4]),
                            'gradAttr'=>explode(',', preg_replace('/\s/', '', $columns[5])),
                            'weight'=>$columns[2],
                            'category'=>$columns[3],
                            'rubrics'=>$columns[6]
                        ];
                    }
                    break;
                case 'Formative Feedback':
                    if ($columns[0] == null) break;
                    if (isset($columns[0]))
                        $curriculum['formativeFeedback'] = $columns[0];
                    break;
                case 'Approaches':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'Header') break;

                    if (isset($columns[0])){
                        $index = count($curriculum['approach'])+1;
                        $curriculum['approach'][$index]['header'] = $columns[0];
                        $curriculum['approach'][$index]['description'] = $columns[1];
                    }
                    break;
                case 'References':
                    if ($columns[0] == null) break;

                    if (isset($columns[0]))
                        array_push($curriculum['reference'], $columns[0]);
                    break;
                case 'Instructors':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'Name') break;

                    if (isset($columns[0]) && isset($columns[3])){
                        $index = count($curriculum['instructor'])+1;
                        $curriculum['instructor'][$index]['name'] = $columns[0];
                        $curriculum['instructor'][$index]['email'] = $columns[3];

                        if (isset($columns[1]) && !empty($columns[1]))
                            $curriculum['instructor'][$index]['office'] = $columns[1];
                        else
                            $curriculum['instructor'][$index]['office'] = null;

                        if (isset($columns[2]) && !empty($columns[2]))
                            $curriculum['instructor'][$index]['phone'] = $columns[2];
                        else
                            $curriculum['instructor'][$index]['phone'] = null;
                    }
                    break;
                case 'Schedule':
                    if ($columns[0] == null) break;
                    else if ($columns[0] == 'Week') break;

                    if (isset($columns[1]) && !empty($columns[1])){
                        $index = count($curriculum['schedule']) + 1;
                        $curriculum['schedule'][$index]['topic'] = $columns[1];
                        if (isset($columns[0]) && !empty($columns[0]))
                            $curriculum['schedule'][$index]['week'] = $columns[0];
                        else
                            $curriculum['schedule'][$index]['week'] = $index;

                        if (isset($columns[2]) && !empty($columns[2]))
                            $curriculum['schedule'][$index]['readings'] = $columns[2];
                        else
                            $curriculum['schedule'][$index]['readings'] = null;

                        if (isset($columns[3]) && !empty($columns[3]))
                            $curriculum['schedule'][$index]['activities'] = $columns[3];
                        else
                            $curriculum['schedule'][$index]['activities'] = null;

                        if (isset($columns[4]) && !empty($columns[4]))
                            $curriculum['schedule'][$index]['LO'] = explode(', ', $columns[4]);
                        else
                            $curriculum['schedule'][$index]['LO'] = null;
                    }
                    break;
                case 'Appendix':
                    if($columns[0] == null) {
                        $isAppendix = True;
                        break;
                    }
                    else if($columns[0] == 'ID'){
                        $currentAppendixID = $columns[1];
                        $isAppendix = True;
                        break;
                    }
                    else if ($columns[0] == '---Criteria---'){
                        $isAppendix = False;
                        break;
                    }

                    if ($isAppendix === True){
                        $curriculum['appendix'][$currentAppendixID][strtolower($columns[0])] = $columns[1];
                    }
                    else if ($isAppendix === False){
                        if ($columns[0] == 'Header') $currentCriteriaID++;
                        $curriculum['appendix'][$currentAppendixID]['criteria'][$currentCriteriaID][strtolower($columns[0])] = $columns[1];
                    }

                    break;
            }
        }
//        echo json_encode($curriculum, JSON_PRETTY_PRINT);

        CommonFunc::store($curriculum);
        return redirect('/curriculum/'.$curriculum['course']['code']);
    }

    public function export($code){
        $course = DB::connection('mysql_curriculum')->table('course')
            ->where('code', '=', $code)
            ->first();
        foreach($course as $header=>$value) $curriculum['Main Details'][$header]=$value;

        $prerequisite = DB::connection('mysql_curriculum')->table('prerequisite')
            ->where('course_code', '=', $code)
            ->get();
        if(count($prerequisite) != 0)
            foreach($prerequisite as $value) $curriculum['Pre-requisite'][]=$value->prerequisiteCode;
        else $curriculum['Pre-requisite'] = null;

        $contactHour = DB::connection('mysql_curriculum')->table('contactHour')
            ->where('course_code', '=', $code)
            ->select(['Lecture','TEL','Tutorial','Lab','ExampleClass'])
            ->first();
        if(!empty($contactHour))
            foreach($contactHour as $header=>$value) $curriculum['Contact Hour'][$header]=$value;
        else $curriculum['Contact Hour'] = null;

        $objectives = DB::connection('mysql_curriculum')->table('objectives')
            ->where('course_code', '=', $code)
            ->first();
        if(!empty($objectives))
            $curriculum['Course Aims']=$objectives->courseAims;
        else $curriculum['Course Aims'] = null;

        $learningOutcomes = DB::connection('mysql_curriculum')->table('learningOutcomes')
            ->where('learningOutcomes.course_code', '=', $code)
            ->leftJoin('lo_gradAttr', function($join){
                $join->on('learningOutcomes.ID', '=', 'lo_gradAttr.lo_ID');
                $join->on('learningOutcomes.course_code', '=', 'lo_gradAttr.course_code');
            })
            ->groupBy('learningOutcomes.ID', 'learningOutcomes.description')
            ->selectRaw('learningOutcomes.ID, learningOutcomes.description,
                GROUP_CONCAT(lo_gradAttr.gradAttrID SEPARATOR \', \') as gradAttr ')
            ->get();
        if(count($learningOutcomes) != 0)
            foreach($learningOutcomes as $LO){
                $curriculum['Learning Outcomes'][$LO->ID]['description']=$LO->description;
                $curriculum['Learning Outcomes'][$LO->ID]['gradAttr']=$LO->gradAttr;
            }
        else $curriculum['Learning Outcomes'] = null;
        $contentAtt = DB::connection('mysql_curriculum')->table('contentAtt')
            ->where('course_code', '=', $code)
            ->select('att1', 'att2')
            ->first();
        if(!empty($contentAtt)){
            foreach ($contentAtt as $header=>$value) $curriculum['Content Att'][$header] = $value;
        }
        else $curriculum['Content Att']=null;
        $contents = DB::connection('mysql_curriculum')->table('content')
            ->join('contentAttDetails', function($join){
                $join->on('content.course_code', '=', 'contentAttDetails.course_code');
                $join->on('content.ID', '=', 'contentAttDetails.content_ID');
            })
            ->where('content.course_code', '=', $code)
            ->select('content.ID', 'content.topics', 'contentAttDetails.details1', 'contentAttDetails.details2', 'contentAttDetails.rowspan')
            ->get();
        if(count($contents) != 0)
            foreach($contents as $content){
                $curriculum['Content'][$content->ID]['Topic']=$content->topics;
                $curriculum['Content'][$content->ID]['details1']=$content->details1;
                $curriculum['Content'][$content->ID]['details2']=$content->details2;
                $curriculum['Content'][$content->ID]['rowspan']=$content->rowspan;
            }
        else $curriculum['Content'] = null;
        $assessments_component = DB::connection('mysql_curriculum')->table('assessment')
            ->where('assessment.course_code', '=', $code)
            ->leftjoin('assessment_category', function ($join){
                $join->on('assessment.course_code', '=', 'assessment_category.course_code');
                $join->on('assessment.ID', '=', 'assessment_category.assessment_ID');
            })
            ->get();
        if(count($assessments_component) != 0)
            foreach($assessments_component as $assessment){
                $curriculum['Assessment'][$assessment->ID]['component'] = $assessment->component;
                $curriculum['Assessment'][$assessment->ID]['weightage'] = $assessment->weightage;
                $curriculum['Assessment'][$assessment->ID]['component'] = $assessment->component;
                $curriculum['Assessment'][$assessment->assessment_ID]['category'] = $assessment->category;
            }
        else $curriculum['Assessment'] = null;
        $assessments_lo = DB::connection('mysql_curriculum')->table('assessment_lo')
            ->where('course_code', '=', $code)
            ->groupBy('assessment_ID')
            ->selectRaw('assessment_ID as ID, GROUP_CONCAT(lo_ID SEPARATOR \', \') as LOs ')
            ->get();
        if(count($assessments_lo) != 0 && $curriculum['Assessment'] != null)
            foreach($assessments_lo as $assessment){
                $curriculum['Assessment'][$assessment->ID]['LOs'] = $assessment->LOs;
            }
        $assessments_gradAttr = DB::connection('mysql_curriculum')->table('assessment_gradAttr')
            ->where('course_code', '=', $code)
            ->groupBy('assessment_ID')
            ->selectRaw('assessment_ID as ID, GROUP_CONCAT(gradAttrID SEPARATOR \', \') as gradAttr ')
            ->get();
        if(count($assessments_gradAttr) != 0 && $curriculum['Assessment'] != null)
            foreach($assessments_gradAttr as $assessment){
                $curriculum['Assessment'][$assessment->ID]['gradAttr'] = $assessment->gradAttr;
            }
        $assessments_rubrics = DB::connection('mysql_curriculum')->table('rubrics')
            ->where('course_code', '=', $code)
            ->select(['assessment_id', 'description'])
            ->get();
        if(count($assessments_rubrics) != 0 && $curriculum['Assessment'] != null)
            foreach($assessments_rubrics as $rubric){
                $curriculum['Assessment'][$rubric->assessment_id]['rubrics'] = $rubric->description;
            }
        $formativeFeedback = DB::connection('mysql_curriculum')->table('formativeFeedback')
            ->where('course_code', '=', $code)
            ->first();
        if(!empty($formativeFeedback))
            $curriculum['Formative Feedback'] = $formativeFeedback->description;
        else $curriculum['Formative Feedback'] = null;
        $approaches = DB::connection('mysql_curriculum')->table('approach')
            ->where('course_code', '=', $code)
            ->get();
        if(count($approaches) != 0)
            foreach($approaches as $approach){
                $curriculum['Approaches'][$approach->ID]['approach'] = $approach->approach;
                $curriculum['Approaches'][$approach->ID]['description'] = $approach->description;
            }
        else $curriculum['Approaches'] = null;
        $references = DB::connection('mysql_curriculum')->table('reference')
            ->where('course_code', '=', $code)
            ->get();
        if(count($references) != 0)
            foreach($references as $reference){
                $curriculum['References'][$reference->ID]['description'] = $reference->description;
            }
        else $curriculum['References'] = null;
        $instructors = DB::connection('mysql_curriculum')->table('instructor')
            ->where('course_code', '=', $code)
            ->join('academicStaff', 'instructor.academicStaffID', '=', 'academicStaff.ID')
            ->select('academicStaff.ID', 'academicStaff.name', 'academicStaff.office', 'academicStaff.phone', 'academicStaff.email')
            ->get();
        if(count($instructors) != 0)
            foreach($instructors as $instructor){
                $curriculum['Instructors'][$instructor->ID]['name'] = $instructor->name;
                $curriculum['Instructors'][$instructor->ID]['office'] = $instructor->office;
                $curriculum['Instructors'][$instructor->ID]['phone'] = $instructor->phone;
                $curriculum['Instructors'][$instructor->ID]['email'] = $instructor->email;
            }
        else $curriculum['Instructors'] = null;
        $schedules = DB::connection('mysql_curriculum')->table('schedule')
            ->where('schedule.course_code', '=', $code)
            ->join('schedule_lo', 'schedule.weekID', '=', 'schedule_lo.weekID')
            ->groupBy('schedule.weekID', 'schedule.topic', 'schedule.readings', 'schedule.activities')
            ->selectRaw('schedule.weekID, schedule.topic, schedule.readings, schedule.activities,
                GROUP_CONCAT(schedule_lo.loID SEPARATOR \', \') as LOs')
            ->get();
        if(count($schedules) != 0)
            foreach($schedules as $schedule){
                $curriculum['Schedule'][$schedule->weekID]['topic'] = $schedule->topic;
                $curriculum['Schedule'][$schedule->weekID]['readings'] = $schedule->readings;
                $curriculum['Schedule'][$schedule->weekID]['activities'] = $schedule->activities;
                $curriculum['Schedule'][$schedule->weekID]['LOs'] = $schedule->LOs;
            }
        else
            $curriculum['Schedule'] = null;
        $appendixes = DB::connection('mysql_curriculum')->table('appendix')
            ->where('appendix.course_code', '=', $code)
            ->leftJoin('criteria', 'appendix.ID', '=', 'criteria.appendixID')
            ->groupBy('appendix.ID', 'appendix.header', 'appendix.description')
            ->selectRaw('appendix.ID, appendix.header, appendix.description,
                GROUP_CONCAT(criteria.ID SEPARATOR \'__\') as criteria_IDs,
                GROUP_CONCAT(criteria.header SEPARATOR \'__\') as criteria_header,
                GROUP_CONCAT(criteria.fail SEPARATOR \'__\') as criteria_fail,
                GROUP_CONCAT(criteria.pass SEPARATOR \'__\') as criteria_pass,
                GROUP_CONCAT(criteria.high SEPARATOR \'__\') as criteria_high')
            ->get();
        if(count($appendixes) != 0)
            foreach($appendixes as $appendix){
                $curriculum['Appendix'][$appendix->ID]['header'] = $appendix->header;
                $curriculum['Appendix'][$appendix->ID]['description'] = $appendix->description;
                if(!empty($appendix->criteria_header)){
                    foreach(explode('__', $appendix->criteria_header) as $cKey => $cHeader){
                        $curriculum['Appendix'][$appendix->ID]['Criteria'][$cKey]['header'] = explode('__', $appendix->criteria_header)[$cKey];
                        $curriculum['Appendix'][$appendix->ID]['Criteria'][$cKey]['fail'] = explode('__', $appendix->criteria_fail)[$cKey];
                        $curriculum['Appendix'][$appendix->ID]['Criteria'][$cKey]['pass'] = explode('__', $appendix->criteria_pass)[$cKey];
                        $curriculum['Appendix'][$appendix->ID]['Criteria'][$cKey]['high'] = explode('__', $appendix->criteria_high)[$cKey];
                    }
                }
            }
        else
            $curriculum['Appendix'] = null;

//        echo json_encode($curriculum, JSON_PRETTY_PRINT);
//        die;
        $this->to_csv($curriculum);
    }
}
