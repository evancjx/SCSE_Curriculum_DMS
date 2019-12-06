<?php
class section{
  public function retrieve(){
    return get_object_vars($this);
  }
}
class gradAttr{
  public $gradAttr;

  function __construct(){}

  public function GradAttr($gradAttr){
    $this->gradAttr = explode(", ", $gradAttr);
  }
}
class contactHour{
  public $Lecture;
  public $TEL;
  public $Tutorial;
  public $LAB;
  public $ExampleClass;

  function __construct($contactHour){
    $this->Lecture = $contactHour['lecture'];
    $this->TEL = $contactHour['tel'];
    $this->Tutorial = $contactHour['tutorial'];
    $this->LAB = $contactHour['lab'];
    $this->ExampleClass = $contactHour['exampleclass'];
  }
}
class prerequisite{
  public $course;
  public $code;
  public $title;

  function __construct($details)
  {
    $this->course = $details['course'];
    $this->code = $details['code'];
    $this->title = $details['title'];
  }
}
class mainDetails extends section {
  public $course;
  public $code;
  public $title;
  public $noAU;
  public $contactHour;
  public $courseAims;

  function __construct($details){
    $this->course = $details['course'];
    $this->code = $details['code'];
    $this->title = $details['title'];
    if(isset($details['noAU'])) $this->noAU = $details['noAU'];
    $contactHour = [
      'lecture' => $details['lecture'],
      'tel' => $details['tel'],
      'tutorial' => $details['tutorial'],
      'lab' => $details['lab'],
      'exampleclass' => $details['exampleclass'],
    ];
    $this->contactHour = new contactHour($contactHour);
    if(isset($details['courseAims'])) $this->courseAims = $details['courseAims'];
  }

}
class learningOutcomes extends gradAttr {
  public $description;

  function __construct($description){
    parent::__construct();
    $this->description = $description;
  }
}
class content {
  public $topic;
  public $details1;
  public $details2;
  public $rowspan;

  function __construct($content){
    $this->topic = $content['topic'];
    $this->details1 = $content['details1'];
    $this->details2 = $content['details2'];
    $this->rowspan = $content['rowspan'];
  }
}
class assessment extends gradAttr {
  public $component;
  public $weightage;
  public $category;
  public $rubrics;
  public $loTested;

  function __construct($assessment){
    parent::__construct();
    $this->component = $assessment['component'];
    $this->weightage = $assessment['weightage'];
    $this->category = $assessment['category'];
    $this->rubrics = $assessment['rubrics'];
  }

  public function LOTested($LOs){
    $this->loTested = explode(", ", $LOs);
  }
}
class approach{
  public $title;
  public $description;

  function __construct($details){
    $this->title = $details['main'];
    $this->description = $details['description'];
  }
}
class instructors{
  public $instructorName;
  public $instructorOffice;
  public $instructorPhone;
  public $instructorEmail;

  function __construct($instructor){
    $this->instructorName = $instructor['instructorName'];
    if(!empty($instructor['instructorOffice']))
      $this->instructorOffice = $instructor['instructorOffice'];
    if(!empty($instructor['instructorPhone']))
      $this->instructorOffice = $instructor['instructorPhone'];
    $this->instructorEmail = $instructor['instructorEmail'];
  }
}
class schedule{
  public $topic;
  public $readings;
  public $activities;
  public $loIDs;

  function __construct($schedule){
    $this->topic = $schedule['topic'];
    $this->readings = $schedule['readings'];
    $this->activities = $schedule['activities'];
    $this->loIDs($schedule['loIDs']);
  }
  private function loIDS ($loIDs){
    $this->loIDs = explode(", ", $loIDs);
  }
}
class appendix{
  public $header;
  public $description;
  public $criteria;

  function __construct($appendix){
    $this->header = $appendix['header'];
    $this->description = $appendix['description'];
  }

  public function criteria($criteria){
    $this->criteria[$criteria['ID']] = new criteria($criteria);
  }
}
class criteria{
  public $header;
  public $fail;
  public $pass;
  public $high;

  public function __construct($criteria){
    $this->header = $criteria['header'];
    $this->fail = $criteria['fail'];
    $this->pass = $criteria['pass'];
    $this->high = $criteria['high'];
  }
}

class courseCurriculum {
  private $mainDetails, $prerequisite, $preRequisiteFor, $learningOutcomes, $cosContentAtt, $content, $assessment,
    $formativeFeedback, $approach, $reference, $courseInstructors, $schedule, $appendix;
  private $displayCH;

  function __construct($data){
    $this->mainDetails = new mainDetails($data['courseMainDetails']);
    if(isset($data['prerequisite'])) $this->prerequisite($data['prerequisite']);
    if(isset($data['preRequisiteFor'])) $this->preRequisiteFor($data['preRequisiteFor']);
    $this->learningOutcomesFunction($data['learning_outcomes'], $data['mapCosLOGradAttr']);
    $this->contentFunction($data['cosContentAtt'], $data['content']);
    $this->assessmentFunction($data['assessment'],$data['assessmentLOTested'], $data['assessmentGradAttr']);
    $this->formativeFeedback = $data['formativeFeedback'];
    $this->approachFunction($data['approach']);
    $this->referenceFunction($data['reference']);
    $this->instructorFunction($data['courseInstructors']);
    $this->scheduleFunction($data['schedule']);
    $this->appendixFunction($data['appendix']);
    $this->criteriaFunction($data['criteria']);
    $this->displayCH = $data['displayCH'];
  }
  function prerequisite($prerequisite){
    foreach($prerequisite as $course){
      $this->prerequisite[] = new prerequisite($course);
    }
  }
  function preRequisiteFor($preRequisiteFor){
    foreach($preRequisiteFor as $course){
      $this->preRequisiteFor[] = new prerequisite($course);
    }
  }
  function learningOutcomesFunction($learningOutcomes, $mapCosLOGradAttr){
    foreach($learningOutcomes as $key => $description){
      $this->learningOutcomes[$key] = new learningOutcomes($description);
    }
    foreach($mapCosLOGradAttr as $key => $value){
      $this->learningOutcomes[$key]->GradAttr($value);
    }
  }
  function contentFunction($cosContentAtt, $content){
    $this->cosContentAtt = $cosContentAtt;
    foreach ($content as $key => $details){
      $this->content[$key] = new content($details);
    }
  }
  function assessmentFunction($assessment, $LOTested, $GradAttr){
    foreach($assessment as $key => $value) {
      $this->assessment[$key] = new assessment($value);
    }
    foreach($LOTested as $key => $value){
      $this->assessment[$key]->LOTested($value);
    }
    foreach($GradAttr as $key => $value){
      $this->assessment[$key]->GradAttr($value);
    }
  }
  function approachFunction($approach){
    foreach($approach as $key => $details){
      $this->approach[$key] = new approach($details);
    }
  }
  function referenceFunction($references){
    foreach($references as $key => $description){
      $this->reference[$key] = $description;
    }
  }
  function instructorFunction($instructors){
    foreach ($instructors as $ID => $details){
      $this->courseInstructors[$ID] = new instructors($details);
    }
  }
  function scheduleFunction($schedule){
    foreach ($schedule as $week => $details){
      $this->schedule[$week] = new schedule($details);
    }
  }
  function appendixFunction($appendix){
    foreach($appendix as $key => $details){
      $this->appendix[$key] = new appendix($details);
    }
  }
  function criteriaFunction($criteria){
    foreach($criteria as $details){
      $this->appendix[$details['appendixID']]->criteria($details);
    }
  }


  public function retrieve(){
    return [
      "courseMainDetails" => $this->mainDetails->retrieve(),
      "prerequisite" => $this->prerequisite,
      "preRequisiteFor" => $this->preRequisiteFor,
      "learning_outcomes" => $this->learningOutcomes,
      "cosContentAtt" => $this->cosContentAtt,
      "content" => $this->content,
      "assessment" => $this->assessment,
      "formativeFeedback" => $this->formativeFeedback,
      "approach" => $this->approach,
      "reference" => $this->reference,
      "courseInstructors" => $this->courseInstructors,
      "schedule" => $this->schedule,
      "appendix" => $this->appendix,
      "displayCH" => $this->displayCH,
    ];
  }
}
