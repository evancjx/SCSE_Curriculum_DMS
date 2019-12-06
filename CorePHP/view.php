<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';

Page::ForceLogin();

$user = new User($_SESSION['user_id']);

include 'includes/header.php';

$c = new Curriculum();
$data = $c->controllerCurriculum($_SERVER);
?>
<body>
	<div class="content">
    <table id="courseDetails" class="table">
      <tr><td class='header' colspan="7">
				<?php
				$courseCodeSplit = explode(" ", $data['courseMainDetails']['course']);
				$courseSplit = explode("/", $courseCodeSplit[0]);
				$courseCode = "";
				foreach($courseSplit as $key => $item){
					if($key == 1) $courseCode.= " and ";
					$courseCode.= $item;
				}
				echo $courseCode." ".$data['courseMainDetails']['code']." − ".$data['courseMainDetails']['title'];
				?>
			</td></tr>
      <tr><td class='label'>Academic Year</td><td colspan="2"></td><td class='label' colspan="2">Semester</td><td colspan="2"></td></tr>
			<tr><td class='label'>Author(s)</td><td class='details' colspan="6"></td></tr>
      <tr><td class='label'>Course Code</td><td class='details' colspan="6"><?php echo $data['courseMainDetails']['course']." ".$data['courseMainDetails']['code'] ?></td></tr>
      <tr><td class='label'>Course Title</td><td class='details' colspan="6"><?php echo $data['courseMainDetails']['title'] ?></td></tr>
      <tr><td class='label'>Pre-requisites</td><td class='details' colspan="6">
        <?php
        if (!empty($data['prerequisite'])){
					foreach($data['prerequisite'] as $key => $item){
						if($key > 0) echo nl2br(", \n ");
						$course = $c->getRequisiteForTitle($item);
						echo $course['course']." ".$item." - ".$course['title'];
					}
				}
				else echo "NIL";
        ?>
      </td></tr>
			<tr><td class='label'>Pre-requisite for</td><td class='details' colspan="6">
				<?php
				if(!empty($data['preRequisiteFor']) && !in_array('NIL', $data['preRequisiteFor'])){
					foreach($data['preRequisiteFor'] as $key => $item){
						if ($key > 0) echo nl2br(", \n");
						$course = $c->getRequisiteForTitle($item);
						echo $course['course']." ".$item." - ".$course['title'];
					}
				}
				else echo 'NIL';
				?>
			</td></tr>
      <tr><td class='label'>No of AUs</td><td class='details' colspan="6"><?php echo $data['courseMainDetails']['noAU'] ?></td></tr>
      <tr><td class='label'>Contact Hours</td>
        <td class='chlabel'><?php echo $data['displayCH'][0] ?></td>
        <td class='chshow'><?php echo $data['contactHour'][preg_replace('/\s/', '', $data['displayCH'][0])] ?></td>
        <td class='chlabel'><?php echo $data['displayCH'][1] ?></td>
        <td class='chshow'><?php echo $data['contactHour'][preg_replace('/\s/', '', $data['displayCH'][1])] ?></td>
        <td class='chlabel'><?php echo $data['displayCH'][2] ?></td>
        <td class='chshow'><?php echo $data['contactHour'][preg_replace('/\s/', '', $data['displayCH'][2])] ?></td></tr>
			<tr><td class='label'>Proposal Date</td><td class='details' colspan="6"></td></tr>
    </table>
    <table id="objectives" class="table">
      <tbody>
        <tr><td class='label'>Course Aims</td></tr>
        <tr><td><?php echo nl2br($data['courseAims']) ?></td></tr>
        <tr><td class='label'>Intended Learning outcomes</td></tr>
        <tr><td>By the end of this course, the student would be able to:<br>
					<?php utilities::displayLearningOutcomes($data['learning_outcomes']); ?>
				</td></tr>
      </tbody>
    </table>
  </div>
	<div class="content pagebreak" <?php if(empty($data['content'])) echo "style='display:none;'" ?>>
		<table id="content" class="table">
		  <tr><td class='label bbl'>Course Contents [<?php echo $data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']?>]</td></tr>
		  <tr><td class='btl'>
		    <table id='cosContents' class='subtable'>
		      <tr>
		        <td class='short'></td>
		        <td class='label'>Topics</td>
		        <td class='att'><?php echo($data['cosContentAtt']['att1']) ?></td>
		        <td class='att'><?php echo($data['cosContentAtt']['att2']) ?></td>
		      </tr>
					<?php utilities::displayCourseContent($data['content']); ?>
				</table>
			</td></tr>
		</table>
	</div>
	<div class="content pagebreak" <?php if(empty($data['assessment'])) echo "style='display:none;'" ?>>
		<table id="assessment" class="table">
			<tr><td class='label bbl'>Assessment [<?php echo $data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']?>]</td></tr>
			<tr><td class='bbl'>includes both continous and summative assessment</td></tr>
			<tr><td class='btl'>
				<table id='cosAssessment' class='subtable'>
					<tr>
						<td class='label medCol'>Component</td>
						<td class='label shortCol'>Course LO Tested</td>
						<td class='label shortCol'>Related Programme LO or Graduate Attributes</td>
						<td class='label shortCol'>Weightage</td>
						<td class='label shortCol'>Team / Individual</td>
						<td class='label shortCol'>Assessment Rubrics</td>
					</tr>
					<?php utilities::displayAssessment($data['assessment'], $data['assessmentLOTested'], $data['assessmentGradAttr'])?>
				</table>
			</td></tr>
		</table>
	</div>
	<div class="pagebreak landscape" <?php if(empty($data['mapCosLOGradAttr'])) echo "style='display:none;'" ?>>
		<table id="tblMapGraduateAttributes" class='table'>
		  <tr><td class='label bbl'>Mapping of Course SLOs to EAB Graduate Attibutes</td></tr>
		  <tr><td class='btl'>
		    <table id="MapGraduateAttributes" class="subtable">
					<!--First Row  -->
		      <tr>
		        <?php
		        $gradAttrList = json_decode($c->getGraduateAttribute(),true);?>
		        <td class='label longCol' rowspan="2">Course Student Learning Outcomes</td>
		        <td class='label catCol' rowspan="2">Cat</td>
		        <td class='label longCol' colspan="<?php echo sizeof($gradAttrList) ?>">EAB's <?php echo sizeof($gradAttrList) ?> Graudate Attributes*</td>
		        <td class='label reqCol' colspan="2">EAB's CE/CS Requirement</td>
		      </tr>
					<!-- Second Row -->
		      <tr>
		        <?php
		        foreach($gradAttrList as $gradAttr){
							$gradAttr_count[$gradAttr['ID']] = 0; //Prepare to count grad attributes
		          $display = "<td class='shortCol'>(".$gradAttr['ID'].")</td>";
		          echo $display;
		        }
		        ?>
		        <td>CE</td><td>CS</td>
		      </tr>
					<!-- Third Row -->
		      <tr>
		        <td id="courseCodeLabel"><?php echo $data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']?></td>
		        <td>Core</td>
		        <?php
						foreach($data['mapCosLOGradAttr'] as $values){
							foreach(explode(', ', $values) as $key){
								$gradAttr_count[$key] += 1;
							}
						}

						foreach($gradAttrList as $gradAttr){
							$display = "<td class='shortCol'>";
							$percentage = $gradAttr_count[$gradAttr['ID']]/count($data['learning_outcomes']);
							switch (1) {
								case $percentage >= 0.75:
									$display.="<img src='\\assets\\full-dot.png' style='width:14px'>";
									break;
								case $percentage >= 0.50:
									$display.="<img src='\\assets\\half-dot.png' style='width:14px'>";
									break;
								case $percentage >= 0.25:
									$display.="<img src='\\assets\\empty-dot.png' style='width:14px'>";
									break;
								default:
									$display.="<img src='\\assets\\blank-dot.png' style='width:14px'>";
									break;
							}
							$display.= "</td>";
							echo $display;
						}
		        ?>
		        <td class='shortCol'><?php if(in_array('CE', explode('/',$data['courseMainDetails']['course']))) echo "<img src='\\assets\\full-dot.png' style='width:14px'>" ?></td>
						<td class='shortCol'><?php if(in_array('CZ', explode('/',$data['courseMainDetails']['course']))) echo "<img src='\\assets\\full-dot.png' style='width:14px'>" ?></td>
		      </tr>
					<!-- Forth Row -->
		      <tr>
		        <td>Overall Statement</td>
		        <td id="mappingCourseAims" colspan="<?php echo sizeof($gradAttrList)+1 ?>"><?php echo $data['courseAims']?></td>
		        <td colspan="2"></td>
		      </tr>
					<?php
					for($i = 1; $i <= count($data['learning_outcomes']); $i++){
						$display =
							"<tr><td>"
							.$i.". ".$data['learning_outcomes'][$i].
							"</td><td colspan='".(count($gradAttrList)+1)."'>";
						if(!empty($data['mapCosLOGradAttr'][$i])) $display.= $data['mapCosLOGradAttr'][$i];
						$display.=
							"</td><td colspan='2'>".
							"</td></tr>";
						echo $display;
					}
					?>
		    </table>
		  </td></tr>
		</table>
	</div>
	<div class="pagebreak landscape">
		<?php
			$gradAttr = $c->getGraduateAttribute();
			$gradAttrList = json_decode($gradAttr,true);
			include 'includes/sectors/graduateattributes.sec.inc.php';
		?>
	</div>
	<div class='content pagebreak' <?php if(empty($data['formativeFeedback'])) echo "style='display:none;'" ?>>
		<table id="tblFormativeFeedback" class='table'>
		  <tr><td class='label'>Formative Feedback [<?php echo $data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']?>]</td></tr>
		  <tr><td class=''><?php echo nl2br($data['formativeFeedback']); ?></td></tr>
		</table>
	</div>
	<div class='content <?php if(empty($data['formativeFeedback'])) echo 'pagebreak'; ?>' <?php if(empty($data['approach'])) echo "style='display:none;'" ?>>
		<table id="tblLearningAndTeachingApproach" class='table'>
		  <tr><td class='label bbl'>Learning and Teaching approach</td></tr>
		  <tr><td class='btl'>
		    <table id="LearningAndTeachingApproach" class="subtable">
		      <tr>
		        <td class='label medCol'>Approach</td>
		        <td class='label'>How does this approach support students in achieving the learning outcomes?</td>
		      </tr>
					<?php utilities::displayApproach($data['approach']);?>
		    </table>
		  </td></tr>
		</table>
	</div>
	<div class='content <?php if(empty($data['formativeFeedback']) && empty($data['approach'])) echo 'pagebreak'; ?>' <?php if(empty($data['reference'])) echo "style='display:none;'" ?>>
		<table id="tblReadingAndReferences" class='table'>
			<tr><td class='label'>Reading and References</td></tr>
			<tr><td class='bbl'>The course will not use any specific text book. The following books and websites will be used as reference materials.</td></td>
			<tr><td class='btl'>
				<ol id="ReadingAndReferences">
					<?php utilities::displayReference($data['reference']);?>
				</ol>
			</td></tr>
		</table>
	</div>
	<?php $common = $c->getCommon();?>
	<div class='content <?php if(empty($data['formativeFeedback']) && empty($data['approach']) && empty($data['reference'])) echo 'pagebreak'; ?>'>
		<table id="tblCoursePoliciesAndStudentResponsibilities" class='table'>
			<tr><td class='label'>Course Policies And Student Responsibilities</td></tr>
			<tr><td class=''><?php echo nl2br($common['Course Policies And Student Responsibilities']['description']); ?></td></tr>
		</table>
		<table id="tblAcademicIntegrity" class='table'>
			<tr><td class='label'>Academic Integrity</td></tr>
			<tr><td class=''><?php echo nl2br($common['Academic Integrity']['description']); ?></td></tr>
		</table>
	</div>
	<div class="content pagebreak" <?php if(empty($data['courseInstructors'])) echo "style='display:none;'" ?>>
		<table id="tblCourseInstructor" class='table'>
			<tr><td class='label bbl'>Course Instructor [<?php echo $data['courseMainDetails']['course']." ".$data['courseMainDetails']['code']." - ".$data['courseMainDetails']['title']?>]</td></tr>
			<tr><td class='btl'>
				<table id="CourseInstructor" class="subtable">
					<tr>
						<td class='label medCol'>Instructor</td>
						<td class='label medCol'>Office Location</td>
						<td class='label medCol'>Phone</td>
						<td class='label medCol'>Email</td>
					</tr>
					<?php utilities::displayCourseInstructor($data['courseInstructors'])?>
				</table>
			</td></tr>
		</table>
	</div>
	<div class='content' <?php if(empty($data['schedule'])) echo "style='display:none;'" ?>>
		<table id="tblPlannedWeeklySchedule" class='table'>
		  <tr><td class='label bbl'>Planned Weekly Schedule</td></tr>
		  <tr><td class='btl'>
		    <table id="PlannedWeeklySchedule" class="subtable">
		      <tr>
		        <td class='label shortCol'>Week</td>
		        <td class='label medCol'>Topic</td>
		        <td class='label '>Course LO</td>
		        <td class='label '>Readings</td>
		        <td class='label medCol'>Example Activities</td>
		      </tr>
					<?php utilities::displaySchedule($data['schedule']); ?>
		    </table>
		  </td></tr>
		</table>
	</div>

	<div class='content pagebreak' <?php if(empty($data['appendix'])) echo "style='display:none;'" ?> >
		<table id="tblAppendix" class='table'>
			<?php utilities::displayAppendix($data['appendix'], $data['criteria']); ?>
		</table>
	</div>
</body>
</html>
