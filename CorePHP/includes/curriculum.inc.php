<?php
include 'utilities.inc.php';

class Form{
  private $contactHoursLabel = array('Lecture', 'TEL', 'Tutorial', 'Lab', 'Example Class');

  public function getCHlabel() {return $this->contactHoursLabel;}
  public function displaySelectCHoption($id, $selected){
    $display = "<select class='sltch' name='chAtt".$id."' id=".$id."><option></option>";
    foreach($this->contactHoursLabel as $label){
      $display.= "<option ";
      if (strtolower(preg_replace('/\s/', '', $label)) == $selected) $display.= "selected ";
      $display.= "value=".strtolower(preg_replace('/\s/', '', $label)).">".$label."</option>";
    }
    $display.= '</select>';
    echo $display;
  }
}

class Curriculum extends Dbh{
  public function controllerCurriculum($SERVERDATA){
    if ($SERVERDATA['REQUEST_METHOD'] == 'GET'){

      if (isset($_GET['function']) && !empty($_GET['function'])){
        //Set title according to function (insert or update)
        utilities::updateTitle(ucfirst($_GET['function']));

        switch (strtolower($_GET['function'])) {
          case 'update':
            if(!isset($_GET['code']) || empty($_GET['code'])) {header('Location: ' . '/');}

            $data = $this->getCourseCurriculum($_GET['code']);
            $displayCH = utilities::determineShowContactHours($data['contactHour']);
            $data['displayCH'] = $displayCH;
            return $data;
          case 'insert':
            break;
          case 'export':
            $cosCurr = $this->getCourseCurriculum($_GET['code']);
            utilities::export($cosCurr);
            break;
          default:
            header('Location: ' . '/');
            break;
        }
      }
      //View.php
      else if (isset($_GET['code']) && !empty($_GET['code'])){
        $data = $this->getCourseCurriculum($_GET['code']);
        if($data === null) header('Location: ' . '/');

        // utilities::updateTitle($data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']);
        utilities::updateTitle($data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']);
        $displayCH = utilities::determineShowContactHours($data['contactHour']);
        $data['displayCH'] = $displayCH;
        return $data;
      }
    }
    else {
      header('Location: ' . '/');
    }
  }

  public function getCourseList(){
    $this->connect();
    $stmt = $this->pdo->query("SELECT * from course");

    echo "<tr><th class='courseHeader' colspan='2'>Course</th>
		      <th class='AUcol'>AUs</th><th class='AUcol' colspan='2'>Action</th></tr>";
		while($row = $stmt->fetch()){
			$display = "<tr>".
        "<td class='brl' style='text-align:right'>".
        $row['course'].$row['code'].
        "</td>".
				"<td class='tuple bll'>".
				"<a href='/view.php?code=".$row['code']."'>".
				$row['title'].
				"</a></td>".
				"<td class='AUcol' style='text-align:center'>".$row['noAU']."</td>".
        "<td class='Delcol'><input type='button' onclick=\"location.href='curriculum.php?function=update&code=".$row['code']."'\" value='Update'/></td>".
        "<td class='Delcol'><input type='button' onclick=\"location.href='dashboard.php?function=export&code=".$row['code']."'\" value='Export'/></td>".
        // "<td class='Delcol'><input type='button' onclick=\"location.href='query.php?delete=delte&code=".$row['code']."'\" value='Delete' disabled/></td>".
				"</tr>";
			echo $display;
		}
  }

  public function getRequisiteTitle($courseCode){
    $this->connect();
    $stmt = $this->pdo->query("SELECT title FROM course
      WHERE code = '".$courseCode."'");
    $result = $stmt->fetch();
    return $result['title'];
  }

  public function getRequisiteForTitle($courseCode){
    $this->connect();
    $sql = "SELECT course, title FROM course WHERE code='".$courseCode."'";
    $sql = $this->pdo->query($sql);
    $result = $sql->fetch();
    $course = [
      'title' => $result['title'],
      'course'=> $result['course']
    ];
    return $course;
  }

  public function checkExist($courseCode){
    $stmt = $this->pdo->query("SELECT code FROM course WHERE code = '".$courseCode."'");
    if(!($stmt->rowCount()))
      throw new Exception("\"".$courseCode."\" Pre-requisite Course does not exist<br>");
    else return true;
  }

  public function getCourseCurriculum($courseCode){
    try{
      $this->connect();

      //Main Details
      $sql = "SELECT * FROM course WHERE code = '".$courseCode."'";
      $curriculum['courseMainDetails'] = $this->pdo->query($sql)->fetch();

      // Pre-requisite
      $prerequisite = []; // Might be empty
      $sql = "SELECT prerequisiteCode FROM prerequisite WHERE course_code = '".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch())
        $prerequisite[] = $row['prerequisiteCode'];
      $curriculum['preRequisite'] = $prerequisite;

      //Contact Hour
      $sql = "SELECT Lecture, TEL, Tutorial, Lab, ExampleClass FROM contactHour WHERE course_code = '".$courseCode."'";
      $curriculum['contactHour'] =  $this->pdo->query($sql)->fetch();

      //Course Aims
      $sql = "SELECT courseAims FROM objectives WHERE course_code = '".$courseCode."'";
      $sql = $this->pdo->query($sql)->fetch();
      if ($sql != null) $curriculum['courseAims'] = $sql['courseAims'];
      else $curriculum['courseAims'] = null;

      // Pre-requisite for
      $preRequisiteFor = [];
      $sql = "SELECT course_code as preRequisiteFor FROM prerequisite WHERE prerequisiteCode = '".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch())
        $preRequisiteFor[] = $row['preRequisiteFor'];
      $curriculum['preRequisiteFor'] = $preRequisiteFor;

      // Learning Outcomes
      $learning_outcomes = [];
      $sql = "SELECT * FROM learningoutcomes WHERE course_code = '".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch())
        $learning_outcomes[$row['ID']]=$row['description'];
      $curriculum['learning_outcomes'] = $learning_outcomes;

      // Course Content attribute header
      $sql = "SELECT att1, att2 FROM contentatt WHERE course_code = '".$courseCode."'";
      $curriculum['cosContentAtt'] = $this->pdo->query($sql)->fetch();

      // Course Content
      $content = [];
      $sql = "SELECT * FROM content c
        JOIN contentAttDetails ad
        ON c.ID = ad.content_ID AND c.course_code = ad.course_code
        WHERE ad.course_code = '".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $content[$row['content_ID']] = [
          'topic' => $row['topics'],
          'details1' => $row['details1'],
          'details2' => $row['details2'],
          'rowspan' => $row['rowspan']
        ];
      }
      $curriculum['content'] = $content;

      // Assessment
      $assessment = [];
      $sql = "SELECT a.component, a.ID, a.weightage, ac.category, r.description as rubrics
        FROM assessment a
        LEFT JOIN assessment_category ac ON a.ID = ac.assessment_ID AND a.course_code = ac.course_code
        LEFT JOIN rubrics r on a.ID = r.assessment_ID AND a.course_code = r.course_code
        WHERE a.course_code = '".$courseCode."' ORDER BY a.ID ASC";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $assessment[$row['ID']] = [
          'component' => $row['component'],
          'weightage' => $row['weightage'],
          'category' => $row['category'],
          'rubrics' => $row['rubrics']
        ];
      }
      $curriculum['assessment'] = $assessment;

      // Assessment mappings (Course Learning Outcomes tested)
      $assessmentLOTested = [];
      $sql = "SELECT a.ID, GROUP_CONCAT(DISTINCT clt.lo_ID SEPARATOR ', ') as loTested
        FROM assessment a
        LEFT JOIN assessment_lo clt on a.ID = clt.assessment_ID AND a.course_code = clt.course_code
        WHERE a.course_code = '".$courseCode."' GROUP BY a.component ORDER BY a.ID ASC";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $assessmentLOTested[$row['ID']] = $row['loTested'];
      }
      $curriculum['assessmentLOTested'] = $assessmentLOTested;

      // Assessment mappings (Gradudate Atttributes)
      $assessmentGradAttr = [];
      $sql = "SELECT a.ID, GROUP_CONCAT( DISTINCT aga.gradAttrID SEPARATOR ', ') as gradAttr
        FROM assessment a
        JOIN assessment_gradattr aga on a.ID = aga.assessment_ID AND a.course_code = aga.course_code
        WHERE a.course_code = '".$courseCode."' GROUP BY a.component ORDER BY a.ID ASC";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $assessmentGradAttr[$row['ID']] = $row['gradAttr'];
      }
      $curriculum['assessmentGradAttr'] = $assessmentGradAttr;

      // Graduate Attribute mappings (Course Learning Outcomes)
      $mapCosLOGradAttr = [];
      $sql = "SELECT lo_ID, GROUP_CONCAT(DISTINCT gradAttrID SEPARATOR ', ') as graduateAttributesID
        FROM lo_gradattr WHERE course_code='".$courseCode."' GROUP BY lo_ID";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $mapCosLOGradAttr[$row['lo_ID']] = $row['graduateAttributesID'];
      }
      $curriculum['mapCosLOGradAttr'] = $mapCosLOGradAttr;

      // Formative Feedback
      $formativeFeedback = [];
      $sql = "SELECT description FROM formativeFeedback WHERE course_code='".$courseCode."'";
      $result = $this->pdo->query($sql)->fetch();
      $curriculum['formativeFeedback'] = $result['description'];

      // Learning and Teaching Approach
      $approach = [];
      $sql = "SELECT ID, approach, description
        FROM approach WHERE course_code='".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $approach[$row['ID']] = [
          'main' => $row['approach'],
          'description' => $row['description']
        ];
      }
      $curriculum['approach'] = $approach;

      // Reading and References
      $reference = [];
      $sql = "SELECT ID, description
        FROM reference WHERE course_code='".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $reference[$row['ID']] =  $row['description'];
      }
      $curriculum['reference'] = $reference;

      // Instructor
      $instructor = [];
      $sql = "SELECT ID, name, office, phone, email
        FROM instructor i JOIN academicStaff a ON i.academicStaffID = a.ID
        WHERE i.course_code='".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $instructor[$row['ID']] = [
          'instructorName' => $row['name'],
          'instructorOffice' => $row['office'],
          'instructorPhone' => $row['phone'],
          'instructorEmail' => $row['email']
        ];
      }
      $curriculum['courseInstructors'] = $instructor;

      // Weekly schedule
      $schedule = [];
      $sql = "SELECT s.weekID, topic, readings, activities, GROUP_CONCAT(DISTINCT sl.loID SEPARATOR ', ') as loIDs
        FROM schedule s
        JOIN schedule_lo sl
        ON s.weekID = sl.weekID
        WHERE s.course_code='".$courseCode."'
        GROUP BY s.weekID, s.topic, s.readings, s.activities";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $schedule[$row['weekID']] = [
          'topic' => $row['topic'],
          'readings' => $row['readings'],
          'activities' => $row['activities'],
          'loIDs' => $row['loIDs']
        ];
      }
      $curriculum['schedule'] = $schedule;

      //SELECT Appendix
      $appendix = [];
      $sql = "SELECT ID, header, description
        FROM appendix WHERE course_code='".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $appendix[$row['ID']] = [
          'header' => $row['header'],
          'description' => $row['description']
        ];
      }
      $curriculum['appendix'] = $appendix;

      //SELECT criteria
      $criteria = [];
      $sql = "SELECT appendixID, ID, header, fail, pass, high
        FROM criteria WHERE course_code='".$courseCode."'";
      $sql = $this->pdo->query($sql);
      while($row = $sql->fetch()){
        $criteria[] = [
          'appendixID' => $row['appendixID'],
          'ID' => $row['ID'],
          'header' => $row['header'],
          'fail' => $row['fail'],
          'pass' => $row['pass'],
          'high' => $row['high']
        ];
      }
      $curriculum['criteria'] = $criteria;

      return $curriculum;
    }catch(Exception $e){
      echo "<br/><br/><b>Failed</b>: ".$e->getMessage()."<br/>";
      echo "<br/>".$e->getTraceAsString()."<br/>";
      foreach ($e->getTrace() as $key => $row) {
          echo "<br/>" . $row['file'] . " (" . $row['line'] . ")<br/>";
      }
      exit;
    }
  }

  public function insertCurriculum($data){
    try{
      $data['GraduateAttributes'] = json_decode($this->getGraduateAttribute(), true);
      $this->connect();

      // Insert into course Table
      //CHECK COURSE CODE FORMAT
      $format = preg_split('/[^\d]+\K/', preg_replace('/\s/', '', $data['code']));
      if(count($format) != 2) {
        echo 'error';
        die();
      }
      $data['code'] = $format[0]." ".$format[1];
      $course = [
        'courseCode' => $data['code'],
        'courseTitle' => $data['title'],
        'noAU' => $data['noAU']
      ];
      $sql = "INSERT INTO course (code, title, noAU) VALUES (
        :courseCode, :courseTitle, :noAU)";
      $this->pdo->prepare($sql)->execute($course);

      // Insert into prerequisite Table
      if (!empty($data['prerequisite'])){
        // $preRequisites = preg_split('/[,]+/', $data['prerequisite']);
        $preRequisites = explode(',', $data['prerequisite']);
        foreach($preRequisites as $key => $code){
          if($key > 0){
            while ($code[0] == ' ') $code = substr($code, 1);
          }
          if($code == 'NIL'||!($this->checkExist($code))) continue;
          $prerequisite = [
            'courseCode' => $data['code'],
            'prerequisite' => $code
          ];
          $sql = "INSERT INTO prerequisite (course_code, requisiteCode) VALUES (:courseCode, :prerequisite)";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($prerequisite);
        }
      }

      // Insert into contactHour Table
      $inputList = [$data['chAtt1'],$data['chAtt2'],$data['chAtt3']];
      $input = [$data['chInput1'], $data['chInput2'], $data['chInput3']];
      $contactHour = ['courseCode' => $data['code']];
      $ch = ['Lecture', 'TEL', 'Tutorial', 'Lab', 'Example Class'];
      foreach ($ch as $att){
        foreach ($inputList as $key => $inputAtt){
          if($inputAtt == strtolower(preg_replace('/\s/', '', $att))){
            $contactHour[$inputAtt] = $input[$key];
            break;
          } else{
            $contactHour[strtolower(preg_replace('/\s/', '', $att))] = NULL;
          }
        }
      }
      $sql = "INSERT INTO contacthour VALUES (
        :courseCode, :lecture, :tel, :tutorial, :lab, :exampleclass)";
      $this->pdo->prepare($sql)->execute($contactHour);

      // Insert into objectives table
      $objectives = [
        'courseCode' => $data['code'],
        'courseAims' => $data['objectives']['courseAims']
      ];
      $sql = "INSERT INTO objectives (course_code, courseAims) VALUES (
        :courseCode, :courseAims)";
      $this->pdo->prepare($sql)->execute($objectives);

      // Insert into Learning Outcomes
      $LOcount = count($data['objectives']['LO']);
      if($LOcount > 0){
        for ($i = 1; $i <= $LOcount; $i++){
          if(empty($data['objectives']['LO'][$i-1])) continue;
          $LOs = [
            'courseCode' => $data['code'],
            'ID' => $i,
            'LO' => $data['objectives']['LO'][$i-1]
          ];
          $sql = "INSERT INTO learningoutcomes (course_code, id, description) VALUES (
            :courseCode, :ID, :LO)";
          $this->pdo->prepare($sql)->execute($LOs);
        }
      }

      // Insert into ContentAtt
      $cosContentAtt=[
        'courseCode' => $data['code'],
        'att1' => $data['cosContentAtt1'],
        'att2' => $data['cosContentAtt2']
      ];
      $sql = "INSERT INTO contentAtt (course_code, att1, att2) VALUES (
        :courseCode, :att1, :att2)";
      $this->pdo->prepare($sql)->execute($cosContentAtt);

      // Insert into Content
      $count = count($data['topics']);
      if($count > 0){
        for ($i = 0; $i < $count; $i++){
          if(empty($data['topics'][$i])) continue;

          $cosContent=[
            'courseCode' => $data['code'],
            'contentID' => $data['ID'][$i],
            'topics' => $data['topics'][$i]
          ];
          $sql = "INSERT INTO content (course_code, contentID, topics) VALUES (
            :courseCode, :contentID, :topics)";
          $this->pdo->prepare($sql)->execute($cosContent);

          $cosContentAttDetails = [
            'courseCode' => $data['code'],
            'contentID' => $data['ID'][$i],
            'details1' => $data['cosContentDetails1'][$i],
            'details2' => $data['cosContentDetails2'][$i]
          ];
          if(isset($data['merge'])){
            if(in_array($data['ID'][$i-1], $data['merge'])){
              $cosContentAttDetails['rowspan'] = 2;
            }
          }

          if(!in_array('rowspan', $cosContentAttDetails))
            $cosContentAttDetails['rowspan'] = 1;

          $sql = "INSERT INTO contentAttDetails (course_code, content_ID, details1, details2, rowspan) VALUES (
            :courseCode, :contentID, :details1, :details2, :rowspan)";
          $this->pdo->prepare($sql)->execute($cosContentAttDetails);
        }
      }

      // Insert Assessment
      $count = count($data['component']);
      if($count > 0){
        for ($i = 0; $i < $count; $i++){
          if(empty($data['component'][$i])) continue;

          // Insert into Assessment
          $cosAssessment = [
            'courseCode' => $data['code'],
            'assessmentID' => $i+1,
            'component' => $data['component'][$i],
            'weightage' => $data['weightage'][$i]
          ];
          $sql = "INSERT INTO assessment (course_code, ID, component, weightage) VALUES (
            :courseCode, :assessmentID, :component, :weightage)";
          $this->pdo->prepare($sql)->execute($cosAssessment);


          // Insert into Assessment_GraduateAttributes
          if(isset($data['gradAttr'.strval($i+1)])){
            $gradAttrCount = count($data['gradAttr'.strval($i+1)]);
            // echo '$gradAttrCount '.$gradAttrCount.'<br/>';
            if($gradAttrCount > 0){
              for($j = 0; $j < $gradAttrCount; $j++){
                $cosAssessmentGradAttr = [
                  'courseCode' => $data['code'],
                  'assessmentID' => $i+1,
                  'graduateAttributesID' => $data['gradAttr'.strval($i+1)][$j]
                ];
                $sql = "INSERT INTO assessment_gradattr (course_code, assessment_ID, gradAttrID) VALUES (
                  :courseCode, :assessmentID, :graduateAttributesID)";

                $this->pdo->prepare($sql)->execute($cosAssessmentGradAttr);
              }
            }
          }
          // Insert into Assessment_Cat
          $cosAssessmentCat = [
            'courseCode' => $data['code'],
            'assessmentID' => $i+1,
            'cat' => $data['componentCat'.strval($i+1)]
          ];

          $sql = "INSERT INTO assessment_category (course_code, assessment_ID, cat) VALUES (
            :courseCode, :assessmentID, :cat)";
          $this->pdo->prepare($sql)->execute($cosAssessmentCat);

          // Insert into CourseLOTested
          if(isset($data['assessment'.strval($i+1).'LO'])){
            $LOTestedCount = count($data['assessment'.strval($i+1).'LO']);
            if($LOTestedCount > 0){
              for($j = 0; $j < $LOTestedCount; $j++){
                $cosLOTested = [
                  'courseCode' => $data['code'],
                  'learningOutcomesID' => $data['assessment'.strval($i+1).'LO'][$j],
                  'assessmentID' => $i+1,
                ];

                $sql = "INSERT INTO assessment_lo (course_code, lo_ID, assessment_ID) VALUES (
                  :courseCode, :learningOutcomesID, :assessmentID)";
                $this->pdo->prepare($sql)->execute($cosLOTested);
              }
            }
          }

          // Insert into Rubrics
          if($data['assessmentRubrics'][$i] = '') continue;

          $assessmentRubrics = [
            'courseCode' => $data['code'],
            'assessmentID' => $i+1,
            'description' => $data['assessmentRubrics'][$i]
          ];
          $sql = "INSERT INTO rubrics (course_code, assessment_ID, description) VALUES (
            :courseCode, :assessmentID, :description)";
          $this->pdo->prepare($sql)->execute($assessmentRubrics);
        }
      }

      //Insert Mapping of Course SLOs

      // //Insert course percentage to Graduate Attributes
      // for($i = 0; $i < count($data['cosGradAttr']); $i++){
      //   if($data['cosGradAttr'][$i] == 0) continue;
      //   $cosGradAttr = [
      //     'courseCode' => $data['code'],
      //     'graduateAttributesID' => $data['GraduateAttributes'][$i]['ID'],
      //     'percentage' => $data['cosGradAttr'][$i]
      //   ];
      //   $sql = "INSERT INTO course_graduateattributes (courseCode, graduateAttributesID, percentage) VALUES (
      //     :courseCode, :graduateAttributesID, :percentage)";
      //   $this->pdo->prepare($sql)->execute($cosGradAttr);
      // }
      //Insert mapping of learning outcomes to graduate attributes
      for($i = 1; $i <= $LOcount; $i++){
        if(!isset($data['LOgradAttr'.$i])) continue;
        for($j = 0; $j < count($data['LOgradAttr'.$i]); $j++){
          $LOGradAttr = [
            'courseCode' => $data['code'],
            'learningOutcomesID' => $i,
            'graduateAttributesID' => $data['LOgradAttr'.$i][$j]
          ];
          $sql = "INSERT INTO lo_gradattr (course_code, lo_ID, gradAttrID) VALUES (
            :courseCode, :learningOutcomesID, :graduateAttributesID)";
          $this->pdo->prepare($sql)->execute($LOGradAttr);
        }
      }

      //Insert formative Feedback
      if(!empty($data['formativeFeedback'])){
        $formativeFeedback = [
          'courseCode' => $data['code'],
          'description' => $data['formativeFeedback']
        ];
        $sql = "INSERT INTO formativeFeedback (course_code, description) VALUES (
          :courseCode, :description)";
        $this->pdo->prepare($sql)->execute($formativeFeedback);
      }

      //Insert Learning and Teaching Approach
      if(isset($data['approachMain']) && isset($data['approachDescription'])){
        $approachCcount = count($data['approachMain']);
        for ($i = 0; $i < $approachCcount; $i++){
          if($data['approachMain'][$i] == '') continue;
          $approach = [
            'courseCode' => $data['code'],
            'ID' => strval($i+1),
            'main' => $data['approachMain'][$i],
            'description' => $data['approachDescription'][$i]
          ];
          $sql = "INSERT INTO approach (course_code, ID, approach, description) VALUES (
            :courseCode, :ID, :main, :description)";
          $this->pdo->prepare($sql)->execute($approach);
        }
      }

      //Insert Reading and references
      if(isset($data['references']) && !empty($data['references'])){
        $referenceCount = count($data['references']);
        for ($i = 0; $i < $referenceCount; $i++){
          if(empty($data['references'][$i]) || $data['references'][$i] == '') continue;
          $reference = [
            'courseCode' => $data['code'],
            'ID' => $i+1,
            'description' => $data['references'][$i]
          ];
          $sql = "INSERT INTO reference (course_code, ID, description) VALUES (
            :courseCode, :ID, :description)";
          $this->pdo->prepare($sql)->execute($reference);
        }
      }

      //Insert Course instructor
      if(isset($data['instructorName']) && isset($data['instructorEmail'])){
        $instructorCount = count($data['instructorName']);
        for($i = 0; $i < $instructorCount; $i++){
          if(empty($data['instructorName'][$i])) continue;
          $academicStaff = [
            'name' => $data['instructorName'][$i],
            'officeLocation' => $data['instructorOffice'][$i],
            'phone' => $data['instructorPhone'][$i],
            'email' => $data['instructorEmail'][$i]
          ];
          //CHECK if $academicStaff exist <-- REMOVE THIS LATER IF academic staff database is up
          $sql = "SELECT ID FROM academicStaff WHERE email=:email LIMIT 1";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['email' => $data['instructorEmail'][$i]]);
          if(!($sql->rowCount())){
            $sql = "INSERT INTO academicStaff (name, office, phone, email) VALUES (
              :name, :officeLocation, :phone, :email)";
            $this->pdo->prepare($sql)->execute($academicStaff);
            $sql = "SELECT ID FROM academicStaff WHERE email=:email";
            $sql = $this->pdo->prepare($sql);
            $sql->execute(['email' => $data['instructorEmail'][$i]]);
          }
          $result = $sql->fetch();
          $courseInstructor = [
            'courseCode' => $data['code'],
            'ID' => $result['ID']
          ];
          $sql = "INSERT INTO instructor (course_code, academicStaffID) VALUES (
            :courseCode, :ID)";
          $this->pdo->prepare($sql)->execute($courseInstructor);
        }
      }

      //Insert Planned weekly schedule
      if(!empty($data['scheduleTopic']) && !empty($data['scheduleReadings']) &&
        !empty($data['scheduleActivities'])){
        $scheduleCount = count($data['scheduleTopic']);
        for($i = 0; $i < $scheduleCount; $i++){
          if($data['scheduleTopic'][$i] == '') continue;
          $weeklySchedule = [
            'courseCode' => $data['code'],
            'week' => $data['scheduleWeek'][$i],
            'scheduleTopic' => $data['scheduleTopic'][$i],
            'scheduleReadings' => $data['scheduleReadings'][$i],
            'scheduleActivities' => $data['scheduleActivities'][$i]
          ];
          $sql = "INSERT INTO schedule (course_code, weekID, topic, readings, activities) VALUES (
            :courseCode, :week, :scheduleTopic, :scheduleReadings, :scheduleActivities)";
          $this->pdo->prepare($sql)->execute($weeklySchedule);
          if(!isset($data['scheduleLO'.strval($i+1)])) continue;
          for($j = 0; $j < count($data['scheduleLO'.strval($i+1)]); $j++){
            $scheduleLO = [
              'courseCode' => $data['code'],
              'scheduleWeekID' => $data['scheduleWeek'][$i],
              'loID' => $data['scheduleLO'.strval($i+1)][$j]
            ];
            $sql = "INSERT INTO schedule_lo (course_code, weekID, loID) VALUES (
              :courseCode, :scheduleWeekID, :loID)";
            $this->pdo->prepare($sql)->execute($scheduleLO);
          }
        }
      }

      //Insert Appendix
      if(!empty($data['appendixHeader']) && !empty($data['appendixDescription'])){
        $appendixCount = count($data['appendixHeader']);
        for($i = 0; $i < $appendixCount; $i++){
          if(empty($data['appendixHeader'][$i])||empty($data['appendixDescription'][$i])) continue;
          $appendix = [
            'courseCode' => $data['code'],
            'ID' => $i+1,
            'header' => $data['appendixHeader'][$i],
            'description' => $data['appendixDescription'][$i]
          ];
          $sql = "INSERT INTO appendix (course_code, ID, header, description) VALUES (
            :courseCode, :ID, :header, :description)";
          $this->pdo->prepare($sql)->execute($appendix);

          if(!isset($data['assessmentCriteria'.strval($i+1)])) continue;

          $criteriaRowCount = count($data['assessmentCriteria'.strval($i+1)]);
          for($j = 0; $j < $criteriaRowCount; $j++){
            $criteriaRow = [
              'courseCode' => $data['code'],
              'appendixID' => $i+1,
              'ID' => $j + 1,
              'header' => $data['assessmentCriteria'.strval($i+1)][$j],
              'fail' => $data['assessmentFail'.strval($i+1)][$j],
              'pass' => $data['assessmentPass'.strval($i+1)][$j],
              'high' => $data['assessmentHigh'.strval($i+1)][$j],
            ];
            $sql = "INSERT INTO criteria (course_code, appendixID, ID, header, fail, pass, high) VALUES (
              :courseCode, :appendixID, :ID, :header, :fail, :pass, :high)";
            $this->pdo->prepare($sql)->execute($criteriaRow);
          }
        }
      }

      // die();
      $this->pdo->commit();
      header('Location: /');
    }catch(Exception $e){
      $this->pdo->rollBack();
      echo "<br/><br/><b>Failed</b>: ".$e->getMessage()."<br/>";
      echo "<br/>".$e->getTraceAsString()."<br/>";
      foreach ($e->getTrace() as $key => $row) {
          echo "<br/>" . $row['file'] . " (" . $row['line'] . ")<br/>";
      }
      exit;
    }
  }

  public function updateCurriculum($data){
    // echo json_encode($data, JSON_PRETTY_PRINT);
    // die;
    try{
      $this->connect();
      $data['GraduateAttributes'] = json_decode($this->getGraduateAttribute(), true);

      // UPDATE course Table
      $course = [
        'courseCode' => $data['code'],
        'courseTitle' => $data['title'],
        'noAU' => $data['noAU']
      ];
      $sql = "UPDATE course SET title=:courseTitle, noAU=:noAU WHERE code=:courseCode";
      $this->pdo->prepare($sql)->execute($course);

      // UPDATE into prerequisite Table
      $existedPrerequisite = [];
      $sql = "SELECT prerequisiteCode FROM prerequisite WHERE course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql -> fetch())
        $existedPrerequisite[] = $row['prerequisiteCode'];

      if(!empty($data['prerequisite']) && $data['prerequisite'] != 'NIL'){
        $preRequisites = preg_split('/[,]+/', $data['prerequisite']);
        if(sizeof($existedPrerequisite) > count($preRequisites)){
          foreach($existedPrerequisite as $key => $item){
            if(!in_array($item, $preRequisites)){
              $deletePrerequisite = [
                'courseCode' => $data['code'],
                'requisiteCode' => $item
              ];
              $sql = "DELETE FROM prerequisite WHERE course_code=:courseCode AND requisiteCode=:requisiteCode";
              $sql = $this->pdo->prepare($sql)->execute($deletePrerequisite);
            }
          }
        }
        foreach($preRequisites as $key => $code){
          if($key > 0) $code = substr($code, 1);
          if(!($this->checkExist($code))) continue;
          $prerequisite = [
            'courseCode' => $data['code'],
            'prerequisite' => $code
          ];
          $sql = "SELECT prerequisiteCode FROM prerequisite WHERE course_code=:courseCode AND prerequisiteCode=:prerequisite";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($prerequisite);
          if(!(boolean)($sql->rowCount())){
            $sql = "INSERT INTO prerequisite (course_code, prerequisiteCode) VALUES (:courseCode, :prerequisite)";
            $this->pdo->prepare($sql)->execute($prerequisite);
          }
        }
      }
      else{
        foreach($existedPrerequisite as $key => $item){
          $deletePrerequisite = [
            'courseCode' => $data['code'],
            'requisiteCode' => $item
          ];
          $sql = "DELETE FROM prerequisite WHERE courseCode=:courseCode AND requisiteCode=:requisiteCode";
          $sql = $this->pdo->prepare($sql)->execute($deletePrerequisite);
        }
      }

      // UPDATE contacthour Table
      if(isset($data['chAtt1']) && isset($data['chAtt2']) && isset($data['chAtt3'])){
        $inputAttList = [$data['chAtt1'], $data['chAtt2'], $data['chAtt3']];
        $input = [$data['chInput1'], $data['chInput2'], $data['chInput3']];
        $contactHour = ['courseCode' => $data['code']];
        $ch = ['Lecture', 'TEL', 'Tutorial', 'Lab', 'Example Class'];
        foreach ($ch as $att){
          foreach ($inputAttList as $key => $inputAtt){
            if($inputAtt == strtolower(preg_replace('/\s/', '', $att))){
              $contactHour[$inputAtt] = $input[$key];
              break;
            }
            else
              $contactHour[strtolower(preg_replace('/\s/', '', $att))] = NULL; // for empty
          }
        }
        $sql = "UPDATE contacthour SET lecture=:lecture, tel=:tel, tutorial=:tutorial, lab=:lab, exampleclass=:exampleclass
          WHERE course_code=:courseCode";
        $this->pdo->prepare($sql)->execute($contactHour);
      }
      else if(isset($data['contactHour'])){
        $params = ['courseCode' => $data['code']];
        $ch = ['Lecture', 'TEL', 'Tutorial', 'Lab', 'Example Class'];
        foreach($data['contactHour'] as $key => $value){
          $params[strtolower(preg_replace('/\s/', '', $ch[$key]))] = $value;
        }

        $sql = "UPDATE contacthour SET lecture=:lecture, tel=:tel, tutorial=:tutorial, lab=:lab, exampleclass=:exampleclass
          WHERE course_code=:courseCode";
        $this->pdo->prepare($sql)->execute($params);
      }

      // UPDATE into objectives table
      $objectives = [
        'courseCode' => $data['code'],
        'courseAims' => $data['objectives']['courseAims']
      ];
      $sql = "UPDATE objectives SET courseAims=:courseAims WHERE course_code=:courseCode";
      $this->pdo->prepare($sql)->execute($objectives);

      // UPDATE into Learning Outcomes table
      $LOcount = count($data['objectives']['LO']);
      if($LOcount > 0){
        for ($i = 1; $i <= $LOcount; $i++){
          if(empty($data['objectives']['LO'][$i-1])) continue;
          $LOs = [
            'courseCode' => $data['code'],
            'ID' => $i
          ];
          //CHECK if exist
          $sql = "SELECT ID FROM learningoutcomes WHERE course_code=:courseCode AND ID=:ID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($LOs);
          if($sql->fetch())
            $sql = "UPDATE learningoutcomes SET description=:LO WHERE course_code=:courseCode AND id=:ID";
          else
            $sql = "INSERT INTO learningoutcomes (course_code, id, description) VALUES (:courseCode, :ID, :LO)";
          $LOs['LO'] = $data['objectives']['LO'][$i-1];
          $this->pdo->prepare($sql)->execute($LOs);
        }
      }

      // UPDATE into contentAtt table
      $cosContentAtt=[
        'courseCode' => $data['code'],
        'att1' => $data['cosContentAtt1'],
        'att2' => $data['cosContentAtt2']
      ];
      $sql = "UPDATE contentAtt SET att1=:att1, att2=:att2 WHERE course_code=:courseCode";
      $this->pdo->prepare($sql)->execute($cosContentAtt);

      // UPDATE into content table
      // get number of content associated with courseCode
      $existedContentID = [];
      $sql = "SELECT ID FROM content WHERE course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql->fetch())
        $existedContentID[] = $row['ID'];

      // example database has already 4
      // after updated left with 3
      // difference of 1
      $contentCount = count($data['topics']);
      $insertedContentID = [];
      if($contentCount > 0){
        for ($i = 1; $i <= $contentCount; $i++){
          if(empty($data['topics'][$i-1])) continue;

          $cosContent=[
            'courseCode' => $data['code'],
            'contentID' => $data['ID'][$i-1]
          ];
          //CHECK if exist in content
          $sql = "SELECT ID FROM content where course_code=:courseCode AND ID=:contentID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($cosContent);
          $cosContent['topics'] = $data['topics'][$i-1];
          if($sql->fetch())
            $sql = "UPDATE content SET topics=:topics WHERE course_code=:courseCode AND ID=:contentID";
          else
            $sql = "INSERT INTO content (course_code, ID, topics) VALUES (:courseCode, :contentID, :topics)";
          $this->pdo->prepare($sql)->execute($cosContent);
          $insertedContentID[] = $data['ID'][$i-1];

          $cosContentAttDetails=[
            'courseCode' => $data['code'],
            'contentID' => $data['ID'][$i-1],
            'details1' => $data['cosContentDetails1'][$i-1],
            'details2' => $data['cosContentDetails2'][$i-1]
          ];

          if(isset($data['merge']) && in_array($data['ID'][$i-1], $data['merge']))
            $cosContentAttDetails['rowspan'] = 2;
          else if( ($i-2)>-1 && isset($data['merge']) && in_array($data['ID'][$i-2], $data['merge']))
            $cosContentAttDetails['rowspan'] = 0;
          else
            $cosContentAttDetails['rowspan'] = 1;

          //CHECK if exist in attDetails table
          $sql = "SELECT content_ID FROM contentAttDetails where course_code=:courseCode AND content_ID=:contentID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'contentID' => $data['ID'][$i-1]]);
          if($sql->fetch()){
            $sql = "UPDATE contentAttDetails SET details1=:details1, details2=:details2, rowspan=:rowspan
              WHERE course_code=:courseCode AND content_ID=:contentID";
          }
          else
            $sql = "INSERT INTO contentAttDetails (course_code, content_ID, details1, details2, rowspan) VALUES (
            :courseCode, :contentID, :details1, :details2, :rowspan)";

          $this->pdo->prepare($sql)->execute($cosContentAttDetails);
        }
      }
      if(sizeof($existedContentID) > sizeof($insertedContentID)){
        //look for difference to delete
        foreach($existedContentID as $ekey=>$toDel){
          if(!in_array($toDel, $insertedContentID)){
            $deleteContent=[
              'courseCode' => $data['code'],
              'contentID' => $toDel
            ];
            $sql = "DELETE FROM content WHERE course_code=:courseCode AND ID=:contentID";
            $sql = $this->pdo->prepare($sql)->execute($deleteContent);
          }
        }
      }

      //UPDATE assessment
      // var_dump($data['component']);
      // die;
      $count = count($data['component']);
      if($count > 0){
        for ($i = 1; $i <= $count; $i++){
          if(empty($data['component'][$i-1])) continue;

          // Update Assessment table
          $cosAssessment = [
            'courseCode' => $data['code'],
            'assessmentID' => $i,
            'component' => $data['component'][$i-1],
            'weightage' => $data['weightage'][$i-1]
          ];
          // CHECK if exist in assessment
          $sql = "SELECT ID FROM assessment WHERE course_code=:courseCode AND ID=:assessmentID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'assessmentID' => $i]);
          if($sql->fetch())//Exists
            $sql = "UPDATE assessment SET component=:component, weightage=:weightage WHERE course_code=:courseCode AND ID=:assessmentID";
          else
            $sql = "INSERT INTO assessment (course_code, ID, component, weightage) VALUES (:courseCode, :assessmentID, :component, :weightage)";
          $this->pdo->prepare($sql)->execute($cosAssessment);

          // Update Assessment_GraduateAttributes table
          // get list of records already existed in the table
          $existedAssocGradAttr = [];
          $sql = "SELECT gradAttrID FROM assessment_gradattr WHERE course_code=:courseCode AND assessment_ID=:assessmentID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'assessmentID' => $i]);
          // unset($existedAssocGradAttr); //clear array
          while($row = $sql->fetch())
            $existedAssocGradAttr[] = $row['gradAttrID'];

          // echo 'here';
          // var_dump($existedAssocGradAttr);
          // die;
          if(isset($data['gradAttr'.strval($i)])){
            $gradAttrCount = count($data['gradAttr'.strval($i)]);

            //CHECK if existed count is more than newly updated count
            if(sizeof($existedAssocGradAttr) > $gradAttrCount){
              foreach($existedAssocGradAttr as $key => $item){
                if(!in_array($item, $data['gradAttr'.strval($i)])){
                  $deleteExistedAssocGradAttr = [
                    'courseCode' => $data['code'],
                    'assessmentID' => $i,
                    'graduateAttributesID' => $item
                  ];
                  $sql = "DELETE FROM assessment_gradattr WHERE course_code=:courseCode AND assessment_ID=:assessmentID AND gradAttrID=:graduateAttributesID";
                  $this->pdo->prepare($sql)->execute($deleteExistedAssocGradAttr);
                }
              }
            }

            for($j = 0; $j < $gradAttrCount; $j++){
              $cosAssessmentGradAttr = [
                'courseCode' => $data['code'],
                'assessmentID' => $i,
                'graduateAttributesID' => $data['gradAttr'.strval($i)][$j]
              ];
              // CHECK if exist in Assessment_GraduateAttributes table
              $sql = "SELECT assessment_ID FROM assessment_gradattr WHERE course_code=:courseCode and assessment_ID=:assessmentID and gradAttrID=:graduateAttributesID";
              $sql = $this->pdo->prepare($sql);
              $sql->execute($cosAssessmentGradAttr);
              if(!$sql->fetch()){ //if does not exisit, insert
                $sql = "INSERT INTO assessment_gradattr (course_code, assessment_ID, gradAttrID) VALUES (
                  :courseCode, :assessmentID, :graduateAttributesID)";
                $this->pdo->prepare($sql)->execute($cosAssessmentGradAttr);
              }
            }
          }
          else{
            foreach($existedAssocGradAttr as $key => $item){
              // var_dump($item);
              // die;
              $deleteExistedAssocGradAttr = [
                'courseCode' => $data['code'],
                'assessmentID' => $i,
                'graduateAttributesID' => $item
              ];
              $sql = "DELETE FROM assessment_gradattr WHERE course_code=:courseCode AND assessment_ID=:assessmentID AND gradAttrID=:graduateAttributesID";
              $this->pdo->prepare($sql)->execute($deleteExistedAssocGradAttr);
            }
          }

          //Update Assessment_Cat table
          if(isset($data['componentCat'.strval($i)])){
            $cosAssessmentCat = [
              'courseCode' => $data['code'],
              'assessmentID' => $i,
              'category' => $data['componentCat'.strval($i)]
            ];
            // Check if exist in Assessment_Cat table
            $sql = "SELECT category FROM assessment_category WHERE course_code=:courseCode and assessment_ID=:assessmentID and category=:category";
            $sql = $this->pdo->prepare($sql);
            $sql->execute($cosAssessmentCat);
            if($sql->fetch())
              $sql = "UPDATE assessment_category set category=:category WHERE course_code=:courseCode and assessment_ID=:assessmentID";
            else
              $sql = "INSERT INTO assessment_category (course_code, assessment_ID, category) VALUES (:courseCode, :assessmentID, :category)";
            $this->pdo->prepare($sql)->execute($cosAssessmentCat);
          }
          else{
            $sql = "DELETE from assessment_category WHERE course_code=:courseCode and assessment_ID=:assessmentID";
            $this->pdo->prepare($sql)->execute(['courseCode'=>$data['code'], 'assessmentID'=>$i]);
          }


          //Update CourseLOTested table
          //get list of records already exisited in the table
          $existedAssocLO = [];
          $sql = "SELECT lo_ID FROM assessment_lo WHERE course_code=:assessmentCourseCode AND assessment_ID=:assessmentID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['assessmentCourseCode' => $data['code'], 'assessmentID' => $i]);
          while($row = $sql->fetch())
            $existedAssocLO[] = $row['lo_ID'];

          if(isset($data['assessment'.strval($i).'LO'])){
            $LOTestedCount = count($data['assessment'.strval($i).'LO']);
            //CHECK if existed count is more than newly updated count
            if(sizeof($existedAssocLO) > $LOTestedCount){
              foreach($existedAssocLO as $key => $item){
                if(!in_array($item, $data['assessment'.strval($i).'LO'])){
                  $deleteExistedAssocLO = [
                    'courseCode' => $data['code'],
                    'learningOutcomesID' => $item,
                    'assessmentID' => $i,
                  ];
                  $sql = "DELETE FROM assessment_lo WHERE course_code=:courseCode and lo_ID=:learningOutcomesID and assessment_ID=:assessmentID";
                  $sql = $this->pdo->prepare($sql)->execute($deleteExistedAssocLO);
                }
              }
            }

            for($j = 0; $j < $LOTestedCount; $j++){
              $cosLOTested = [
                'courseCode' => $data['code'],
                'learningOutcomesID' => $data['assessment'.strval($i).'LO'][$j],
                'assessmentID' => $i,
              ];
              //Check if exisit in CourseLOTested table
              $sql = "SELECT * FROM assessment_lo WHERE course_code=:courseCode and lo_ID=:learningOutcomesID and assessment_ID=:assessmentID";
              $sql = $this->pdo->prepare($sql);
              $sql->execute($cosLOTested);
              if(!$sql->fetch()){
                $sql = "INSERT INTO assessment_lo (course_code, lo_ID, assessment_ID) VALUES (
                  :courseCode, :learningOutcomesID, :assessmentID)";
                $this->pdo->prepare($sql)->execute($cosLOTested);
              }
            }

          }
          else{
            foreach($existedAssocLO as $key => $item){
              $deleteExistedAssocLO = [
                'courseCode' => $data['code'],
                'learningOutcomesID' => $item,
                'assessmentID' => $i,
              ];
              $sql = "DELETE FROM assessment_lo WHERE course_code=:courseCode and lo_ID=:learningOutcomesID and assessment_ID=:assessmentID";
              $sql = $this->pdo->prepare($sql)->execute($deleteExistedAssocLO);
            }
          }

          //Update Rubrics
          $assessmentRubrics = [
            'courseCode' => $data['code'],
            'assessmentID' => $i
          ];
          $sql = "SELECT assessment_ID FROM rubrics WHERE course_code=:courseCode AND assessment_ID=:assessmentID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($assessmentRubrics);
          $assessmentRubrics['description'] = $data['assessmentRubrics'][$i-1];

          if($sql->fetch())
            $sql = "UPDATE rubrics set description=:description WHERE course_code=:courseCode AND assessment_ID=:assessmentID";
          else
            $sql = "INSERT INTO rubrics (course_code, assessment_ID, description) VALUES (:courseCode, :assessmentID, :description)";
          $this->pdo->prepare($sql)->execute($assessmentRubrics);
        }
      }
      else{
        $sql = "DELETE FROM assessment WHERE course_code=:courseCode";
        $sql = $this->pdo->prepare($sql)->execute(['courseCode' => $data['code']]);
      }

      // // echo json_encode($data, JSON_PRETTY_PRINT);
      // //UPDATE course learning outcomes mapping to EAB graduate attributes
      // //Update course percentage to Graduate attributes
      // if(isset($data['cosGradAttr'])){
      //   for($i = 0; $i < count($data['cosGradAttr']); $i++){
      //     $cosGradAttr = [
      //       'courseCode' => $data['code'],
      //       'graduateAttributesID' => $data['GraduateAttributes'][$i]['ID'],
      //       'percentage' => $data['cosGradAttr'][$i]
      //     ];
      //     //Check if percentage exist
      //     $sql = "SELECT percentage FROM course_graduateattributes WHERE courseCode=:courseCode AND graduateAttributesID=:graduateAttributesID";
      //     $sql = $this->pdo->prepare($sql);
      //     $sql->execute(['courseCode' => $data['code'], 'graduateAttributesID' => $data['GraduateAttributes'][$i]['ID']]);
      //     if($sql->fetch())
      //       $sql = "UPDATE course_GraduateAttributes SET percentage=:percentage WHERE courseCode=:courseCode AND graduateAttributesID=:graduateAttributesID";
      //     else
      //       $sql = "INSERT INTO course_graduateattributes (courseCode, graduateAttributesID, percentage) VALUES (
      //         :courseCode, :graduateAttributesID, :percentage)";
      //     $this->pdo->prepare($sql)->execute($cosGradAttr);
      //   }
      // }

      //Update mapping of learning outcomes to graduate attributes
      for($i = 1; $i <= $LOcount; $i++){
        //get list of records already existed in the table
        $existedLOAssocGradAttr = [];
        $sql = "SELECT gradAttrID from lo_gradattr WHERE course_code=:courseCode AND lo_ID=:learningOutcomesID";
        $sql = $this->pdo->prepare($sql);
        $sql->execute(['courseCode' => $data['code'], 'learningOutcomesID' => $i]);
        while($row = $sql->fetch())
          $existedLOAssocGradAttr[] = $row['gradAttrID'];

        //CHECK if existed count is more than newly updated count
        //delete the extra existing ones
        if(isset($data['LOgradAttr'.$i]) && !empty($data['LOgradAttr'.$i])){
          if(sizeof($existedLOAssocGradAttr) > count($data['LOgradAttr'.$i])){
            foreach($existedLOAssocGradAttr as $key => $item){
              if(!in_array($item, $data['LOgradAttr'.$i])){
                $deleteExisitedLOAssocGradAttr = [
                  'courseCode' => $data['code'],
                  'learningOutcomesID' => $i,
                  'graduateAttributesID' => $item
                ];
                $sql = "DELETE FROM lo_gradattr WHERE course_code=:courseCode AND lo_ID=:learningOutcomesID AND gradAttrID=:graduateAttributesID";
                $sql = $this->pdo->prepare($sql)->execute($deleteExisitedLOAssocGradAttr);
              }
            }
          }
          for($j = 0; $j < count($data['LOgradAttr'.$i]); $j++){
            $LOGradAttr = [
              'loCourseCode' => $data['code'],
              'learningOutcomesID' => $i,
              'graduateAttributesID' => $data['LOgradAttr'.$i][$j]
            ];
            //CHECK if mapping exist
            $sql = "SELECT gradAttrID from lo_gradattr WHERE course_code=:loCourseCode AND lo_ID=:learningOutcomesID AND gradAttrID=:graduateAttributesID";
            $sql = $this->pdo->prepare($sql);
            $sql->execute($LOGradAttr);
            if(!$sql->fetch()){
              $sql = "INSERT INTO lo_gradattr (course_code, lo_ID, gradAttrID) VALUES (
                :loCourseCode, :learningOutcomesID, :graduateAttributesID)";
              $this->pdo->prepare($sql)->execute($LOGradAttr);
            }
          }
        }
        else{
          foreach($existedLOAssocGradAttr as $key => $item){
            $deleteExisitedLOAssocGradAttr = [
              'loCourseCode' => $data['code'],
              'learningOutcomesID' => $i,
              'graduateAttributesID' => $item
            ];
            $sql = "DELETE FROM lo_gradattr WHERE course_code=:loCourseCode AND lo_ID=:learningOutcomesID AND gradAttrID=:graduateAttributesID";
            $sql = $this->pdo->prepare($sql)->execute($deleteExisitedLOAssocGradAttr);
          }
        }
      }

      //Update formative Feedback
      if(isset($data['formativeFeedback']) && $data['formativeFeedback'] != ''){
        $formativeFeedback = [
          'courseCode' => $data['code'],
          'description' => $data['formativeFeedback']
        ];
        $sql = "SELECT description FROM formativeFeedback WHERE course_code=:courseCode";
        $sql = $this->pdo->prepare($sql);
        $sql->execute(['courseCode' => $data['code']]);
        if($sql->fetch())
          $sql = "UPDATE formativeFeedback SET description=:description WHERE course_code=:courseCode";
        else
          $sql = "INSERT INTO formativeFeedback (course_code, description) VALUES (:courseCode, :description)";
        $this->pdo->prepare($sql)->execute($formativeFeedback);
      }
      else{
        $sql = "DELETE from formativeFeedback where course_code=:courseCode";
        $this->pdo->prepare($sql)->execute(['courseCode'=>$data['code']]);
      }

      //Update learning and teaching approach
      $existApproach = [];
      $sql = "SELECT ID FROM approach WHERE course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql->fetch())
        $existApproach[] = $row['ID'];

      if(isset($data['approachMain']) && isset($data['approachDescription'])){
        $approachCcount = count($data['approachMain']);
        if(sizeof($existApproach) > $approachCcount){
          foreach($existApproach as $key => $item){
            if(!in_array($item, $data['approachMain'])){
              $deleteApproach = [
                'courseCode' => $data['code'],
                'ID' => $item
              ];
              $sql = "DELETE FROM approach WHERE course_code=:courseCode AND ID=:ID";
              $sql = $this->pdo->prepare($sql)->execute($deleteApproach);
            }
          }
        }
        for ($i = 0; $i < $approachCcount; $i++){
          if($data['approachMain'][$i] == '') continue;
          $approach = [
            'courseCode' => $data['code'],
            'ID' => strval($i+1),
            'main' => $data['approachMain'][$i],
            'description' => $data['approachDescription'][$i]
          ];

          //CHECK if approach exist
          $sql = "SELECT ID FROM approach WHERE course_code=:courseCode AND ID=:ID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'ID' => $i+1]);
          if($sql->fetch())
            $sql = "UPDATE approach SET approach=:main, description=:description WHERE course_code=:courseCode AND ID=:ID";
          else
            $sql = "INSERT INTO approach (course_code, ID, approach, description) VALUES (:courseCode, :ID, :main, :description)";
          $this->pdo->prepare($sql)->execute($approach);
        }
      }

      //Update Reading and references
      $existReference = [];
      $sql = "SELECT ID FROM reference WHERE course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql->fetch())
        $existReference[] = $row['ID'];

      if(isset($data['references'])){
        $referenceCount = count($data['references']);
        if(sizeof($existReference) > $referenceCount){
          foreach($existReference as $key => $item){
            if(!in_array($item, $data['approachMain'])){
              $deleteReference = [
                'courseCode' => $data['code'],
                'ID' => $item
              ];
              $sql = "DELETE FROM reference WHERE course_code=:courseCode AND ID=:ID";
              $sql = $this->pdo->prepare($sql)->execute($deleteReference);
            }
          }
        }
        for ($i = 0; $i < $referenceCount; $i++){
          if($data['references'][$i] == '') continue;
          $reference = [
            'courseCode' => $data['code'],
            'ID' => $i+1,
            'description' => $data['references'][$i]
          ];

          //CHECK if reference exist
          $sql = "SELECT ID FROM reference WHERE course_code=:courseCode AND ID=:ID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'ID' => $i+1]);
          if($sql->fetch())
            $sql = "UPDATE reference SET description=:description WHERE course_code=:courseCode AND ID=:ID";
          else
            $sql = "INSERT INTO reference (course_code, ID, description) VALUES (
              :courseCode, :ID, :description)";
          $this->pdo->prepare($sql)->execute($reference);
        }
      }

      //UPDATE Course instructor
      // var_dump($data['instructorName']);
      // die;
      $existInstructor = [];
      $sql = "SELECT i.academicStaffID, email FROM instructor i JOIN academicStaff a WHERE i.academicStaffID=a.ID AND i.course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql->fetch())
        $existInstructor[$row['academicStaffID']] = ['email' => $row['email']];

      if(isset($data['instructorName']) || isset($data['instructorOffice']) ||
        isset($data['instructorPhone']) || isset($data['instructorEmail'])){
        $instructorCount = count($data['instructorName']);
        if(sizeof($existInstructor) >= $instructorCount){
          foreach($existInstructor as $key => $item){
            if(!in_array($item, $data['instructorEmail'])){
              $deleteInstructor = [
                'courseCode' => $data['code'],
                'academicStaffID' => $key
              ];
              $sql = "DELETE FROM instructor WHERE course_code=:courseCode AND academicStaffID=:academicStaffID";
              $this->pdo->prepare($sql)->execute($deleteInstructor);
            }
          }
        }
        for($i = 0; $i < $instructorCount; $i++){
          if(empty($data['instructorName'][$i]) || empty($data['instructorEmail'][$i])) continue;
          $academicStaff = [
            'name' => $data['instructorName'][$i],
            'officeLocation' => $data['instructorOffice'][$i],
            'phone' => $data['instructorPhone'][$i],
            'email' => $data['instructorEmail'][$i]
          ];
          //CHECK if $academicStaff exist <-- REMOVE THIS LATER IF academic staff database is up
          $sql = "SELECT ID FROM academicStaff WHERE email=:email";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['email' => $data['instructorEmail'][$i]]);
          if(!($result = $sql->fetch())){
            $sql = "INSERT INTO academicStaff (name, office, phone, email) VALUES (
              :name, :officeLocation, :phone, :email)";
            $this->pdo->prepare($sql)->execute($academicStaff);
            $sql = "SELECT ID FROM academicStaff WHERE email=:email";
            $sql = $this->pdo->prepare($sql);
            $sql->execute(['email' => $data['instructorEmail'][$i]]);
            $result = $sql->fetch();
          }
          $courseInstructor = [
            'courseCode' => $data['code'],
            'ID' => $result['ID']
          ];
          $sql = "SELECT * FROM instructor WHERE course_code=:courseCode AND academicStaffID=:ID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($courseInstructor);
          if(!$sql->fetch()){
            $sql = "INSERT INTO instructor (course_code, academicStaffID) VALUES (
              :courseCode, :ID)";
            $this->pdo->prepare($sql)->execute($courseInstructor);
          }
        }
      }
      else{
        //else condition = [empty list of instructor name]
        $sql = "DELETE from instructor WHERE course_code=:courseCode";
        $this->pdo->prepare($sql)->execute(['courseCode'=>$data['code']]);
      }

      //Update Planned weekly schedule
      $existSchedule = [];
      $sql = "SELECT weekID FROM schedule WHERE course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql->fetch())
        $existSchedule[] = $row['weekID'];

      if(!empty($data['scheduleTopic']) && !empty($data['scheduleReadings']) &&
        !empty($data['scheduleActivities'])){
        $scheduleCount = count($data['scheduleTopic']);
        if(sizeof($existSchedule) > $scheduleCount){
          foreach($existSchedule as $key => $item){
            if(!in_array($item, $data['scheduleTopic'])){
              $deleteSchedule = [
                'courseCode' => $data['code'],
                'weekID' => $item
              ];
              $sql = "DELETE FROM schedule WHERE course_code=:courseCode AND weekID=:weekID";
              $this->pdo->prepare($sql)->execute($deleteSchedule);
            }
          }
        }
        for($i = 0; $i < $scheduleCount; $i++){
          if($data['scheduleTopic'][$i] == '') continue;
          $weeklySchedule = [
            'courseCode' => $data['code'],
            'week' => $data['scheduleWeek'][$i],
            'scheduleTopic' => $data['scheduleTopic'][$i],
            'scheduleReadings' => $data['scheduleReadings'][$i],
            'scheduleActivities' => $data['scheduleActivities'][$i]
          ];
          $sql = "SELECT weekID FROM schedule WHERE course_code=:courseCode AND weekID=:week";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'week' => $data['scheduleWeek'][$i]]);
          if($sql->fetch())
            $sql = "UPDATE schedule SET topic=:scheduleTopic, readings=:scheduleReadings, activities=:scheduleActivities
              WHERE course_code=:courseCode AND weekID=:week";
          else
            $sql = "INSERT INTO schedule (course_code, weekID, topic, readings, activities) VALUES (
              :courseCode, :week, :scheduleTopic, :scheduleReadings, :scheduleActivities)";
          $this->pdo->prepare($sql)->execute($weeklySchedule);

          //if learning outcomes for the week is empty, skip. Continue to next row
          if(!isset($data['scheduleLO'.strval($i+1)])) continue;

          $existWeekLO = [];
          $sql = "SELECT loID FROM schedule_lo WHERE course_code=:courseCode AND weekID=:week";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'week' => $data['scheduleWeek'][$i]]);
          while($row = $sql->fetch()){
            $existWeekLO[] = [
              'loID' => $row['loID']
            ];
          }
          $scheduleLOcount = count($data['scheduleLO'.strval($i+1)]);
          if(sizeof($existWeekLO) > $scheduleLOcount){
            foreach($existWeekLO as $key => $item){
              if(!in_array($item, $data['scheduleLO'.strval($i+1)])){
                $deleteWeekLO = [
                  'scheduleCourseCode' => $data['code'],
                  'scheduleWeekID' => $data['scheduleWeek'][$i],
                  'loID' => $item
                ];
                $sql = "DELETE FROM schedule_lo WHERE course_code=:scheduleCourseCode AND weekID=:scheduleWeekID AND loID=:loID";
                $this->pdo->prepare($sql)->execute($deleteWeekLO);
              }
            }
          }
          for($j = 0; $j < $scheduleLOcount; $j++){
            $scheduleLO = [
              'courseCode' => $data['code'],
              'scheduleWeekID' => $data['scheduleWeek'][$i],
              'loID' => $data['scheduleLO'.strval($i+1)][$j]
            ];
            $sql = "SELECT loID FROM schedule_lo WHERE course_code=:courseCode AND weekID=:scheduleWeekID AND loID=:loID";
            $sql = $this->pdo->prepare($sql);
            $sql->execute($scheduleLO);
            if(!$sql->fetch()){
              $sql = "INSERT INTO schedule_lo (course_code, weekID, loID) VALUES (
                :courseCode, :scheduleWeekID, :loID)";
              $this->pdo->prepare($sql)->execute($scheduleLO);
            }
          }
        }
      }
      else{
        $sql = "DELETE from schedule where course_code=:courseCode";
        $this->pdo->prepare($sql)->execute(['courseCode'=>$data['code']]);
      }

      //Update Appendix
      $existAppendix = [];
      $sql = "SELECT ID FROM appendix WHERE course_code=:courseCode";
      $sql = $this->pdo->prepare($sql);
      $sql->execute(['courseCode' => $data['code']]);
      while($row = $sql->fetch())
        $existAppendix[] = $row['ID'];

      if(sizeof($existAppendix) >= count($data['appendixHeader'])){
        foreach($existAppendix as $key => $item){
          if(!in_array($item, $data['appendixHeader'])){
            $deleteAppendix = [
              'courseCode' => $data['code'],
              'ID' => $item
            ];
            $sql = "DELETE FROM appendix WHERE course_code=:courseCode AND ID=:ID";
            $this->pdo->prepare($sql)->execute($deleteAppendix);
          }
        }
      }

      if(!empty($data['appendixHeader']) && !empty($data['appendixDescription'])){
        $appendixCount = count($data['appendixHeader']);
        for($i = 0; $i < $appendixCount; $i++){
          if(($data['appendixHeader'][$i]) == '') continue;
          $appendix = [
            'courseCode' => $data['code'],
            'ID' => $i+1
          ];
          $sql = "SELECT ID FROM appendix WHERE course_code=:courseCode AND ID=:ID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute($appendix);
          $appendix['header'] = $data['appendixHeader'][$i];
          $appendix['description'] = $data['appendixDescription'][$i];
          if($sql->fetch())
            $sql = "UPDATE appendix SET header=:header, description=:description WHERE course_code=:courseCode AND ID=:ID";
          else
            $sql = "INSERT INTO appendix (course_code, ID, header, description) VALUES (:courseCode, :ID, :header, :description)";
          $this->pdo->prepare($sql)->execute($appendix);

          if(!isset($data['assessmentCriteria'.strval($i+1)])) continue;

          $existCriteria = [];
          $sql = "SELECT ID FROM criteria WHERE course_code=:courseCode AND appendixID=:appendixID";
          $sql = $this->pdo->prepare($sql);
          $sql->execute(['courseCode' => $data['code'], 'appendixID' => $i+1]);
          while($row = $sql->fetch())
            $existCriteria[] = $row['ID'];

          $criteriaRowCount = count($data['assessmentCriteria'.strval($i+1)]);

          if(sizeof($existCriteria) > $criteriaRowCount){
            foreach($existCriteria as $key => $item){
              if(!in_array($item, $data['assessmentCriteria'.strval($i+1)])){
                $deleteCriteria = [
                  'courseCode' => $data['code'],
                  'appendixID' => $i+1,
                  'ID' => $item
                ];
                $sql = "DELETE FROM criteria WHERE course_code=:courseCode AND appendixID=:appendixID AND ID=:ID";
                $this->pdo->prepare($sql)->execute($deleteAppendix);
              }
            }
          }

          for($j = 0; $j < $criteriaRowCount; $j++){
            if(empty($data['assessmentCriteria'.strval($i+1)][$j])) continue;
            $criteriaRow = [
              'courseCode' => $data['code'],
              'appendixID' => $i+1,
              'ID' => $j + 1,
              'header' => $data['assessmentCriteria'.strval($i+1)][$j],
              'fail' => $data['assessmentFail'.strval($i+1)][$j],
              'pass' => $data['assessmentPass'.strval($i+1)][$j],
              'high' => $data['assessmentHigh'.strval($i+1)][$j],
            ];
            $sql = "SELECT ID FROM criteria WHERE course_code=:courseCode AND appendixID=:appendixID AND ID=:ID";
            $sql = $this->pdo->prepare($sql);
            $sql->execute(['courseCode' => $data['code'], 'appendixID' => $i+1, 'ID' => $j + 1]);
            if($sql->fetch())
              $sql = "UPDATE criteria SET header=:header, fail=:fail, pass=:pass, high=:high WHERE course_code=:courseCode AND appendixID=:appendixID AND ID=:ID";
            else
              $sql = "INSERT INTO criteria (course_code, appendixID, ID, header, fail, pass, high) VALUES (:courseCode, :appendixID, :ID, :header, :fail, :pass, :high)";
            $this->pdo->prepare($sql)->execute($criteriaRow);
          }
        }
      }

      // die();
      $this->pdo->commit();
      header('Location: /');
    }catch(Exception $e){
      $this->pdo->rollBack();
      echo "<br/><br/><b>Failed</b>: ".$e->getMessage()."<br/>";
      echo "<br/>".$e->getTraceAsString()."<br/>";
      foreach ($e->getTrace() as $key => $row) {
          echo "<br/>" . $row['file'] . " (" . $row['line'] . ")<br/>";
      }
      exit;
    }
  }

  public function deleteCurriculum($courseCode){
    try{
      $this->connect();
      $delete = [
        'courseCode' => $courseCode
      ];
      $sql = "DELETE FROM course WHERE code=:courseCode";
      $result = $this->pdo->prepare($sql)->execute($delete);
      if($result){
        echo 'Course code: '.$courseCode.' deleted';
        $this->pdo->commit();
        header('Location: /');
      }else{
        header('Location: /error.php');
      }
    }catch(Exception $e){
      $this->pdo->rollBack();
      echo "Failed: ".$e->getMessage();
    }

  }

  public function getGraduateAttribute(){
    $this->connect();
    $stmt = $this->pdo->query("SELECT * from graduateattributes");

    while($row = $stmt->fetch()){
      $gradAttr[]=[
        'ID' => $row['ID'],
        'main' => $row['main'],
        'description' => $row['description']
      ];
    }
    return json_encode($gradAttr);
  }

  public function getLO($courseCode){
    $this->connect();
    $stmt = $this->pdo->query("SELECT ID, description from learningoutcomes WHERE courseCode='".$courseCode."'");
    while($row = $stmt->fetch()){
      $LO[] = [
        'ID' => $row['ID'],
        'description' => $row['description']
      ];
    }
    return json_encode($LO);
  }

  public function getCosLOTested($courseCode){
    $this->connect();
    $stmt = $this->pdo->query("SELECT assessmentID, learningOutcomesID FROM courselotested WHERE assessmentCourseCode='".$courseCode."'");
    while($row = $stmt->fetch()){
      $cosLOTested[] = [
        'assessmentID' => $row['assessmentID'],
        'learningOutcomesID' => $row['learningOutcomesID']
      ];
    }
    return json_encode($cosLOTested);
  }

  public function getCosGradAttrPercent($courseCode){
    $this->connect();
    $stmt = $this->pdo->query("SELECT graduateAttributesID, percentage from course_graduateattributes WHERE courseCode='".$courseCode."'");
    while($row = $stmt->fetch()){
      $cosGradAttrPercent[] = [
        'graduateAttributesID' => $row['graduateAttributesID'],
        'percentage' => $row['percentage']
      ];
    }
    return json_encode($cosGradAttrPercent);
  }

  public function getMapCosGradAttr($courseCode){
    $this->connect();
    $stmt = $this->pdo->query("SELECT learningOutcomesID, graduateAttributesID FROM learningOutcomes_GraduateAttributes
      WHERE loCourseCode='".$courseCode."'");
    while($row = $stmt->fetch()){
      $mapCosLOGradAttr[] = [
        'learningOutcomesID' => $row['learningOutcomesID'],
        'graduateAttributesID' => $row['graduateAttributesID']
      ];
    }

    return json_encode($mapCosLOGradAttr);
  }

  public function getCommon(){
    $this->connect();
    $stmt = $this->pdo->query("SELECT * FROM common");
    while($row = $stmt->fetch()){
      $common[$row['title']] = [
        'description' => $row['description']
      ];
    }

    return $common;
  }

  public function getScheduleLO($courseCode){
    $this->connect();
    $stmt = $this->pdo->query("SELECT weekID, loID FROM schedule_lo
      WHERE course_code='".$courseCode."'");
    while($row = $stmt->fetch()){
      $scheduleLO[] = [
        'scheduleWeek' => $row['weekID'],
        'loID' => $row['loID']
      ];
    }

    return json_encode($scheduleLO);
  }

  public function getPrerequitiesLike($similar){
    $prerequitiesLike = [];
    $this->connect();
    $stmt = $this->pdo->query("SELECT code, title FROM course
      WHERE title LIKE '%".$similar."%' OR code LIKE '%".$similar."%'
      ORDER BY code ASC");
    while($row = $stmt->fetch()){
      // $prerequitiesLike[] = $row;
      $prerequitiesLike[] = [
        'value' => $row['code'],
        'label' => $row['code']." - ".$row['title']
      ];
    }
    return json_encode($prerequitiesLike);
  }

  public function searchCurriculum($data){
    // echo json_encode($data, JSON_PRETTY_PRINT);
    // die;
    $this->connect();
    $sql_queries = $result = [];
    $display_result = $courses = $searchConditions = [];
    if(!empty($data['code']) || !empty($data['title'])){
      array_push($display_result, 'course');

      $condition = "Searching course with ";
      $sql = "SELECT course, code, title, noAU FROM course WHERE ";
      if(!empty($data['code'])){
        $condition.= 'course code of \''.$data['code'].'\'';
        $sql .= "code LIKE '%".$data['code']."%' ";
      }
      if(!empty($data['title'])){

        if(!empty($data['code'])){
          $sql.= "OR  ";
          $condition.= " or ";
        }

        $condition.= 'course title of \''.$data['title'].'\'';
        $sql.= "title LIKE '%".$data['title']."%' ";
      }

      array_push($searchConditions, $condition);
      array_push($sql_queries, $sql);
    }

    if(!empty($data['coursePrerequisite'])){
      // echo json_encode($data, JSON_PRETTY_PRINT);
      // die;
      array_push($display_result, 'coursePrerequisite');
      $sql = "SELECT course, code, title
        FROM course
        WHERE code LIKE '%".$data['coursePrerequisite']."%'
        OR title LIKE '%".$data['coursePrerequisite']."%' ";
      $sql = $this->pdo->query($sql);
      $courses = "";
      if($searchCourse = $sql->fetchAll()){
        foreach ($searchCourse as $key => $course){
          if ($key > 0) $courses.= ", ";
          $courses.= $course['course']." ".$course['code']." ".$course['title'];
        }
      }


      if(isset($data['coursePrerequisiteFor'])){
        $condition = 'Searching \'Pre-requisite for\' course with \''.$data['coursePrerequisite'].'\'';
        if($courses != '') $condition.= '<br>'.$courses.' is pre requisite to the following';
        $sql = "SELECT course, code, title, noAU
          FROM course
          WHERE code IN (
            SELECT course_code
            FROM prerequisite
            WHERE prerequisiteCode = '".$data['coursePrerequisite']."'
            OR prerequisiteCode IN (
              SELECT code from course WHERE title LIKE '%".$data['coursePrerequisite']."%'
            )
          )";
      }
      else{
        $condition = 'Searching \'Pre requisite of\' course with \''.$data['coursePrerequisite'].'\'';
        if($courses != '') $condition.='<br>the following is/are pre requisite(s) to '.$courses;
        $sql = "SELECT course, code, title, noAU
          FROM course
          WHERE code IN (
            SELECT prerequisiteCode
            FROM prerequisite
            WHERE course_code = '".$data['coursePrerequisite']."'
            OR course_code IN (
              SELECT code from course WHERE title LIKE '%".$data['coursePrerequisite']."%'
            )
          )";
      }

      array_push($searchConditions, $condition);
      array_push($sql_queries, $sql);
    }

    if(!empty($data['courseInstructor'])){
      array_push($display_result, 'courseInstructor');

      $condition  = "Searching for instructor with ".$data['courseInstructor'];

      $sql = "SELECT a.name, a.office, a.phone, a.email, GROUP_CONCAT(b.course SEPARATOR ',') as course, GROUP_CONCAT(b.code SEPARATOR ',') as code, GROUP_CONCAT(b.title SEPARATOR ',') as title
        FROM academicstaff a
        JOIN (SELECT i.academicStaffID, c.course, c.code, c.title
          FROM course c
          JOIN instructor i ON c.code = i.course_code
          WHERE i.academicStaffID IN (
            SELECT ID from academicstaff
            WHERE name LIKE '%".$data['courseInstructor']."%'
            OR office LIKE '%".$data['courseInstructor']."%'
            OR phone LIKE '%".$data['courseInstructor']."%'
            OR email LIKE '%".$data['courseInstructor']."%')
          ORDER BY c.code) b
        ON a.ID = b.academicStaffID
        GROUP BY a.ID";

      array_push($searchConditions, $condition);
      array_push($sql_queries, $sql);
    }

    if(!empty($data['contactType'])){
      array_push($display_result, 'contactType');

      $condition  = "Searching for course with contact type of '";
      $sql = "SELECT c.course, c.code, c.title, c.noAU FROM course c JOIN contacthour ch ON ch.course_code = c.code WHERE ";
      for($i = 0; $i < sizeof($data['contactType']); $i++){
        if($i > 0){
          $condition.= ", ";
          $sql.= " OR ";
        }
        $condition.=$data['contactType'][$i];

        $sql.= $data['contactType'][$i]." >= ";
        if(!empty($data['contactHours'][$data['contactType'][$i]])){
          $sql.= $data['contactHours'][$data['contactType'][$i]];
        } else {
          $sql.= "0";
        }
      }

      array_push($searchConditions, $condition."'");
      array_push($sql_queries, $sql);
    }

    if(!empty($data['courseAssessment'])){
      array_push($display_result, 'courseAssessment');

      $condition  = "Searching for course with assessment of '".$data['courseAssessment']."'";
      $sql = "SELECT course, code, title, a.component, a.weightage, GROUP_CONCAT(DISTINCT a.component SEPARATOR ',') as component, GROUP_CONCAT(a.weightage SEPARATOR ',') as weightage
        FROM course c JOIN assessment a ON c.code = a.course_code
        WHERE code IN(
          SELECT course_code FROM assessment WHERE component LIKE '%".$data['courseAssessment']."%')
        GROUP BY c.code";

      array_push($searchConditions, $condition);
      array_push($sql_queries, $sql);
    }

    if(isset($sql_queries)){
      foreach($sql_queries as $key => $sql){
        $sql = $this->pdo->query($sql);
        $result[$display_result[$key]] = $sql->fetchAll();
      }
    }
    if(isset($result) && !empty($result)){
      // echo json_encode($result, JSON_PRETTY_PRINT);
      $result = ['result' => $result];
      $display_result = ['display_result' => $display_result];
      $searchConditions = ['searchConditions' => $searchConditions];
      $result = array_merge($result, $display_result, $searchConditions);
      // echo json_encode($result, JSON_PRETTY_PRINT);
      // die;
      return $result;
    }
    else{
      return ['error' => 'No result'];
    }
  }
}
?>
