<?php
class utilities{
  static function export($cosCurr){
    // echo json_encode($cosCurr, JSON_PRETTY_PRINT); exit;

    foreach ($cosCurr['assessmentLOTested'] as $key => $value) {
      $cosCurr['assessment'][$key]['LOTested'] = $value;
    }
    unset($cosCurr['assessmentLOTested']);
    foreach ($cosCurr['assessmentGradAttr'] as $key => $value) {
      $cosCurr['assessment'][$key]['gradAttr'] = $value;
    }
    unset($cosCurr['assessmentGradAttr']);
    foreach ($cosCurr['learning_outcomes'] as $key => $value) {
      unset($cosCurr['learning_outcomes'][$key]);
      $cosCurr['learning_outcomes'][$key]['description'] = $value;
    }
    foreach ($cosCurr['mapCosLOGradAttr'] as $key => $value) {
      $cosCurr['learning_outcomes'][$key]['gradAttr'] = $value;
    }
    unset($cosCurr['mapCosLOGradAttr']);
    // echo json_encode($cosCurr, JSON_PRETTY_PRINT); exit;

    $filename = $cosCurr['courseMainDetails']['course']." ".$cosCurr['courseMainDetails']['code']." - ".$cosCurr['courseMainDetails']['title'].".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    ob_end_clean();
    $fp = fopen('php://output', 'w');

    foreach($cosCurr as $section=>$sectionFields){
      if($section == 'preRequisiteFor' || $section == 'cosGradAttrPercent') continue;
      switch($section){
        case 'courseMainDetails':
          fputcsv($fp, array('Course Main Details'));
          foreach($sectionFields as $header => $details){
            fputcsv($fp, array($header, $details));
          }
          break;
        case 'preRequisite':
          fputcsv($fp, array('Pre-requisite'));
          // if(sizeof($sectionFields) == 0) fputcsv($fp, array(''));
          foreach($sectionFields as $row){
            fputcsv($fp, array($row));
          }
          break;
        case 'contactHour':
          fputcsv($fp, array('Contact Hour'));
          $header = ['Lecture', 'TEL', 'Tutorial', 'Lab', 'Example Class'];
          fputcsv($fp, $header);
          $contactHour = [];
          foreach($sectionFields as $header => $value){
            array_push($contactHour, $value);
          }
          fputcsv($fp, $contactHour);
          break;
        case 'courseAims':
          fputcsv($fp, array('Course Aims'));
          fputcsv($fp, [$sectionFields]);
          break;
        case 'learning_outcomes':
          fputcsv($fp, array('Learning Outcomes'));
          fputcsv($fp, ['Description', null, null, null, 'Grad Attributes']); // Header
          foreach($sectionFields as $key => $detail){
            $row = [
              $detail['description'],
              null,
              null,
              null
            ];
            if(isset($detail['gradAttr']))
              $row[] = $detail['gradAttr'];
            else
              $row[] = '';
            fputcsv($fp, $row);
          }
          break;
        case 'cosContentAtt':
          fputcsv($fp, array('Course\'s Content Attribute'));
          fputcsv($fp, ['Att 1', 'Att 2']); // Header
          if($sectionFields == null) break;
          fputcsv($fp, [$sectionFields['att1'], $sectionFields['att2']]);
          break;
        case 'content':
          fputcsv($fp, array('Content'));
          $header = ['S/N', 'Topics', '[att1]', '[att2]', 'Rowspan'];
          fputcsv($fp, $header);
          if(empty($sectionFields)){
            fputcsv($fp, array());
          }
          else{
            foreach($sectionFields as $rows => $rowDetails){
              $merge = [];
              array_push($merge, $rows);
              foreach($rowDetails as $header => $details){
                array_push($merge, $details);
              }
              fputcsv($fp, $merge);
            }
          }
          break;
        case 'assessment':
          fputcsv($fp, array('Assessment'));
          $header = ['S/N', 'Component', 'Weightage', 'Category', 'Rubrics', 'LO Tested', 'Grad Attributes'];
          fputcsv($fp, $header);
          if(empty($sectionFields)){
            fputcsv($fp, array());
          }
          else{
            foreach($sectionFields as $rows => $rowDetails){
              $merge = [];
              array_push($merge, $rows);
              foreach($rowDetails as $header => $details){
                array_push($merge, $details);
              }
              fputcsv($fp, $merge);
            }
          }
          break;
        case 'formativeFeedback':
          fputcsv($fp, array('Formative Feedback'));
          fputcsv($fp, array($sectionFields));
          break;
        case 'approach':
          fputcsv($fp, array('Approach'));
          $header = ['S/N', 'Title', 'Description'];
          fputcsv($fp, $header);
          if(empty($sectionFields)){
            fputcsv($fp, array());
          }
          else{
            foreach($sectionFields as $id=>$rowDetails){
              $merge = [];
              array_push($merge, $id);
              foreach($rowDetails as $title => $description){
                array_push($merge, $description);
              }
              fputcsv($fp, $merge);
            }
          }
          break;
        case 'reference':
          fputcsv($fp, array('References'));
          $header = ['S/N', 'Description'];
          fputcsv($fp, $header);
          if(empty($sectionFields)){
            fputcsv($fp, array());
          }
          else{
            foreach($sectionFields as $sn => $description){
              fputcsv($fp, array($sn, $description));
            }
          }
          break;
        case 'courseInstructors':
          fputcsv($fp, array('Course Instructors', '', 'To add more, copy the headers [instructorName, instructorOffice, instructorPhone, instructorEmail] under the existing headers'));
          if(empty($sectionFields)){
            fputcsv($fp, array('instructorName'));
            fputcsv($fp, array('instructorOffice'));
            fputcsv($fp, array('instructorPhone'));
            fputcsv($fp, array('instructorEmail'));
          }
          else{
            foreach($sectionFields as $id => $details){
              foreach($details as $header => $detail){
                fputcsv($fp, array($header, $detail));
              }
            }
          }
          break;
        case 'schedule':
          fputcsv($fp, array('Schedule'));
          $header = ['Week', 'Topic', 'Readings', 'Activities', 'Learning Outcomes'];
          fputcsv($fp, $header);
          if(empty($sectionFields)){
            fputcsv($fp, array());
          }
          else{
            foreach($sectionFields as $week => $details){
              $merge = [];
              array_push($merge, $week);
              foreach($details as $title => $detail){
                array_push($merge, $detail);
              }
              fputcsv($fp, $merge);
            }
          }
          break;
        case 'appendix':
          fputcsv($fp, array('Appendix'));
          $header = ['ID', 'Title', 'Description'];
          fputcsv($fp, $header);
          if(empty($sectionFields)){
            fputcsv($fp, array());
          }
          else{
            foreach($sectionFields as $id => $details){
              $merge = [];
              array_push($merge, $id);
              foreach($details as $title => $description){
                array_push($merge, $description);
              }
              fputcsv($fp, $merge);
            }
          }
          break;
        case 'criteria':
          fputcsv($fp, array('Criteria', '', 'To add more, copy the headers [appendixID, header, fail, pass, high] under the existing headers'));
          if(empty($sectionFields)){
            fputcsv($fp, array('appendixID'));
            fputcsv($fp, array('header'));
            fputcsv($fp, array('fail'));
            fputcsv($fp, array('pass'));
            fputcsv($fp, array('high'));
          }
          else{
            foreach($sectionFields as $row){
              foreach($row as $header => $detail){
                if($header == 'ID') continue;
                fputcsv($fp, array($header, $detail));
              }
            }
          }
          break;
      }
      fputcsv($fp, array('', '', '', '', '', 'Seperator [keep this row between each sections]'));// blank line
    }
    exit;
  }
  static function updateTitle($title){
    echo '<title>'.$title.' - FYP</title>';
  }
  static function determineShowContactHours($data){
    $displayCH = [];
    if($data['Lecture'] != ""){
      array_push($displayCH, 'Lecture');
    }
    if($data['TEL'] != ""){
      array_push($displayCH, 'TEL');
    }
    if($data['Tutorial'] != ""){
      array_push($displayCH, 'Tutorial');
    }
    if($data['Lab'] != ""){
      array_push($displayCH, 'Lab');
    }
    if($data['ExampleClass'] != ""){
      array_push($displayCH, 'Example Class');
    }
    if(empty($displayCH)){
      array_push($displayCH, 'Lecture', 'Tutorial', 'Lab');
    }
    return $displayCH;
  }
  static function displayLearningOutcomes($LO){
    $display = '<ol>';
    foreach ($LO as $lo){
      $display.= '<li>'.$lo.'</li>';
    }
    $display.= '</ol>';
    echo $display;
  }
  static function displayCourseContent($contents){
    foreach ($contents as $key => $content) {
      $display =
        "<tr id='topics".$key."'>".
        "<td class='short sn'>".$key."</td>".
        "<td class='des'>".nl2br($content['topic'])."</td>".
        "<td class='att1'>".$content['details1']."</td>";
      if($content['rowspan'] > 0){
        $display.="<td class='att2' rowspan=".$content['rowspan'].">".nl2br($content['details2'])."</td>";
      }
      echo $display;
    }
  }
  static function displayAssessment($assessment, $assessmentLOTested, $assessmentGradAttr){
    foreach ($assessment as $index => $item){
      $display =
        "<tr id='assessment".$index."'><td>".
        $index.". ".$item['component'].
        "</td><td>";
      if(!empty($assessmentLOTested[$index])) $display.= $assessmentLOTested[$index];
      $display.= "</td><td>";
      if(!empty($assessmentGradAttr[$index])) $display.=  $assessmentGradAttr[$index];
      $display.= "</td><td>".
        $item['weightage']."%".
        "</td><td>".
        $item['category'].
        "</td><td>".
        $item['rubrics'].
        "</td></tr>";
      echo $display;
    }
  }
  static function displayApproach($approach){
    foreach($approach as $key => $content){
      echo "<tr><td>".
        $content['main'].
        "</td><td>".
        $content['description'].
        "</td></tr>";
    }
  }
  static function displayReference($reference){
    foreach($reference as $content){
      echo "<li>".
        $content.
        "</li>";
    }
  }
  static function displayCourseInstructor($courseInstructor){
    foreach($courseInstructor as $key => $content){
      echo "<tr><td>".
        $content['instructorName'].
        "</td><td>".
        $content['instructorOffice'].
        "</td><td>".
        $content['instructorPhone'].
        "</td><td>".
        $content['instructorEmail'].
        "</td></tr>";
    }
  }
  static function displaySchedule($schedule){
    foreach($schedule as $key => $content){
      echo "<tr><td>".
        $key.
        "</td><td>".
        $content['topic'].
        "</td><td>";
      if(isset($content['loIDs']))
        echo $content['loIDs'];
      echo "</td><td>".
        $content['readings'].
        "</td><td>".
        $content['activities'].
        "</td></tr>";
    }
  }
  static function displayAppendix($appendix, $criteria){
    foreach($appendix as $Akey => $item){
      $display = "<tr id='appendixHeader".$Akey."' class='appendix header'><td>".
        "Appendix ".$Akey.": ".$item['header'].
        "</td></tr>".
        "<tr id='appendixDescription".$Akey."' class='appendix'><td>";
      if($item['description'] != '')
        $display.= nl2br($item['description']);
      else
        $display.= "[Empty Description] Please update";
      $display.= "</td></tr>";
      echo $display;
      $bTableHeader = false;
      $bTableFooter = true;

      foreach($criteria as $Ckey => $criteriaItem){
        if($Akey != $criteriaItem['appendixID']) continue;
        if(!$bTableHeader){
          $bTableHeader = true;
          echo "<tr id='appendixCriteriaTable".$Akey."'><td colspan='2' class='btl bll brl bbl'>".
            "<table id='criteria".$Akey."' class='criteriaTable'>".
            "<tr><td rowspan='2' class='criteriaHeader'>".
            nl2br("Criteria\r\n for Appendix ").$Akey.
            "</td><td colspan='3'>".
            "Standards".
            "</td></tr>".
            "<tr><td class='midCol'>".
            nl2br("Fail Standard\r\n(0-39%)").
            "</td><td class='midCol'>".
            nl2br("Pass Standard\r\n(40-80%)").
            "</td><td class='midCol'>".
            nl2br("High Standard\r\n(81-100%)").
            "</td></tr>";
            $bTableFooter = false;
        }
        echo "<tr id='criteriaTableRow".$Ckey."' class='criteriaRow'><td>";
        echo nl2br($criteriaItem['header']);
        echo "</td><td>";
        if ($criteriaItem['fail'] != '')
          echo nl2br($criteriaItem['fail']);
        else
          echo "Empty, please update.";
        echo "</td><td>";
        if ($criteriaItem['pass'] != '')
          echo nl2br($criteriaItem['fail']);
        else
          echo "Empty, please update.";
        echo "</td><td>";
        if ($criteriaItem['high'] != '')
          echo nl2br($criteriaItem['high']);
        else
          echo "Empty, please update.";
        echo "</td></tr>";
      }
      if(!$bTableFooter){
        $bTableFooter = true;
        echo "</table></td></tr>";
      }
    }
  }
  static function displaySearchResult($result){
    if(empty($result['result']) && isset($result['error'])){
      echo '<br/>'.$result['error'].'<br/>';
      return;
    }
    // echo json_encode($result, JSON_PRETTY_PRINT);
    foreach ($result['display_result'] as $key => $displayType) {
      switch ($displayType) {
        case 'course':
        case 'contactType':
        case 'coursePrerequisite':
          $display = '<table id="resultTbl" class="" style="width:600px">';
          if(empty($result['result'][$displayType])){
            $display.= '<tr><td colspan="3" style="border:0">'.$result['searchConditions'][$key].'</td></tr>';
            $display.= '<tr><td colspan="3">No result</td></tr>';
          }
          else{
            $display.= '<tr><td colspan="3" style="border:0">'.$result['searchConditions'][$key].'</td></tr>'.
              '<th colspan="2">Course</th><th>AUs</th>';
          }
          foreach ($result['result'][$displayType] as $course){
            $display.= '<tr>'.
              '<td class="midCol brl" style="text-align:right">'.$course['course'].' '.$course['code'].'</td>'.
              "<td class='bll'><a href='/view.php?code=".$course['code']."'><b>".$course['title']."</b></a></td>".
              '<td class="midCol">'.$course['noAU'].'</td>'.
              '</tr>';
          }
          $display.= '</tr>';
          echo $display;
          break;

        case 'courseAssessment':
          $display = '<table id="resultTbl" class="resultTbl">';
          $display.= '<tr><td colspan="3" style="border:0">'.$result['searchConditions'][$key].'</td></tr>';
          foreach($result['result'][$displayType] as $course){
            $display.= '<tr><td class="resultHeader">'.$course['course'].' '.$course['code'].' <b><a href="/view.php?code='.$course['code'].'">'.$course['title'].'</a></b></td></tr>';
            $components = explode(',', $course['component']);
            $weightages = explode(',', $course['weightage']);

            $display.= '<tr><td><table class="resultInnerTbl"><tr><th>Component</th><th>Weightage</th></tr>';

            for($i = 0; $i < sizeof($components); $i++){
              $display.= '<tr><td class="longCol">'.$components[$i].'</td><td class="shortCol">'.$weightages[$i].'</td></tr>';
            }
            $display.= '</table></td></tr>';
          }
          echo $display;
          break;

        case 'courseInstructor':
          // echo json_encode($result, JSON_PRETTY_PRINT);
          $display = '<table id="resultTbl" class="resultTbl">';
          $display.= '<tr><td colspan="3" style="border:0">'.$result['searchConditions'][$key].'</td></tr>';
          foreach($result['result'][$displayType] as $row){
            $display.= '<tr><td class="resultHeader"><b>'.$row['name'].'</b> <a href="mailto: '.$row['email'].'">'.$row['email'].'</a></td></tr>';
            $course = explode(',', $row['course']);
            $courseCode = explode(',', $row['code']);
            $courseTitle = explode(',', $row['title']);

            $display.= '<tr><td><table class="resultInnerTbl"><tr><th>Course Code</th><th>Course Title</th></tr>';

            for($i = 0; $i < sizeof($courseCode); $i++){
              $display.= '<tr>
                <td class="midCol"><a href="/view.php?code='.$courseCode[$i].'">'.$course[$i].$courseCode[$i].'</a></td>
                <td class="longCol"><a href="/view.php?code='.$courseCode[$i].'">'.$courseTitle[$i].'</a></td></tr>';
            }
            $display.= '</table></td></tr>';
          }
          echo $display;
          break;
        default:
          break;
      }
    }
  }
}
?>
