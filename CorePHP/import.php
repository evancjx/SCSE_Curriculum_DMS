<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';
include 'includes/header.php';

Page::ForceLogin();
$user = new User($_SESSION['user_id']);

if (isset($_POST['submit'])) {
	$fileName = $_FILES["file"]["tmp_name"];
	if ($_FILES["file"]["size"] > 0) {
		$file = fopen($fileName, "r");
		$sections = [
			'Course Main Details',
			'Pre-requisite',
			'Contact Hour',
			'Course Aims',
			'Learning Outcomes',
			'Course\'s Content Attribute',
			'Content',
			'Assessment',
			'Formative Feedback',
			'Approach',
			'References',
			'Course Instructors',
			'Schedule',
			'Appendix',
			'Criteria'
		];
		$contactType = [];
		$contactHour = [];
		while(($columns = fgetcsv($file, 10000, ",")) !== FALSE){
			if(in_array($columns[0], $sections)){
				$currentSection = $columns[0];
				switch ($currentSection) {
					case 'Pre-requisite':
						$curriculum['prerequisite'] = [];
						break;
					case 'Learning Outcomes':
						$curriculum['objectives']['LO'] = [];
						break;
					case 'Content':
						$curriculum['ID'] = [];
						$curriculum['topics'] = [];
						$curriculum['cosContentDetails1'] = [];
						$curriculum['cosContentDetails2'] = [];
						$curriculum['merge'] = [];
						break;
					case 'Assessment':
						$curriculum['component'] = [];
						$curriculum['weightage'] = [];
						$curriculum['assessmentRubrics'] = [];
					case 'Approach':
						$curriculum['approachMain'] = [];
						$curriculum['approachDescription'] = [];
						break;
					case 'References':
						$curriculum['references'] = [];
						break;
					case 'Course Instructors':
						$curriculum['instructorName'] = [];
						$curriculum['instructorOffice'] = [];
						$curriculum['instructorPhone'] = [];
						$curriculum['instructorEmail'] = [];
						break;
					case 'Schedule':
						$curriculum['scheduleWeek'] = [];
						$curriculum['scheduleTopic'] = [];
						$curriculum['scheduleReadings'] = [];
						$curriculum['scheduleActivities'] = [];
					case 'Appendix':
						$curriculum['appendixHeader'] = [];
						$curriculum['appendixDescription'] = [];
						break;
					case 'Criteria':
						$currentAppendixID = 0;
					default:
						// code...
						break;
				}
				continue;
			}
			switch ($currentSection) {
				case 'Course Main Details':
					if($columns[0] == null) break;
					else if($columns[0] == 'courseAims'){
						$curriculum['objectives'][$columns[0]] = $columns[1];
						break;
					}
					$curriculum[$columns[0]] = $columns[1];
					break;
				case 'Pre-requisite':
					if($columns[0] == null) break;
					array_push($curriculum['prerequisite'], $columns[0]);
					break;
				case 'Contact Hour':
					if($columns[0] == null) break;
					else if($columns[0] == 'Lecture') break;

					if (isset($columns[0]) and $columns[0] != ''){
						array_push($contactType, 'lecture');
						array_push($contactHour, $columns[0]);
					}
					if (isset($columns[1]) and $columns[1] != ''){
						array_push($contactType, 'tel');
						array_push($contactHour, $columns[1]);
					}
					if (isset($columns[2]) and $columns[2] != ''){
						array_push($contactType, 'tutorial');
						array_push($contactHour, $columns[2]);
					}
					if (isset($columns[3]) and $columns[3] != ''){
						array_push($contactType, 'lab');
						array_push($contactHour, $columns[3]);
					}
					if (isset($columns[4]) and $columns[4] != ''){
						array_push($contactType, 'exampleclass');
						array_push($contactHour, $columns[4]);
					}

					// array_push($curriculum['contactHour'], $columns[0], $columns[1], $columns[2], $columns[3], $columns[4]);
					break;
				case 'Course Aims':
					if($columns[0] == null) break;
					$curriculum['objectives']['courseAims'] = $columns[0];
					// var_dump($curriculum);
					break;
				case 'Learning Outcomes':
					if($columns[0] == null) break;
					else if($columns[0] == 'Description') break;
					array_push($curriculum['objectives']['LO'], $columns[0]);
					if(isset($columns[4]) and $columns[4] != '') $curriculum['LOgradAttr'.sizeof($curriculum['objectives']['LO'])] = explode(', ', $columns[4]);
					break;
				case 'Course\'s Content Attribute':
					if($columns[0] == null) break;
					else if($columns[0] == 'Att 1') break;
					$curriculum['cosContentAtt1'] = $columns[0];
					$curriculum['cosContentAtt2'] = $columns[1];
					break;
				case 'Content':
					if($columns[0] == null) break;
					else if($columns[0] == 'S/N') break;
					array_push($curriculum['ID'], $columns[0]);
					array_push($curriculum['topics'], $columns[1]);
					array_push($curriculum['cosContentDetails1'], $columns[2]);
					array_push($curriculum['cosContentDetails2'], $columns[3]);
					if((int)$columns[4] > 1)
						array_push($curriculum['merge'], $columns[0]);
					break;
				case 'Assessment':
					if($columns[0] == null) break;
					else if($columns[0] == 'S/N') break;
					array_push($curriculum['component'], $columns[1]);
					array_push($curriculum['weightage'], $columns[2]);
					if(isset($columns[3]) and $columns[3] != '') $curriculum['componentCat'.$columns[0]] = $columns[3];
					array_push($curriculum['assessmentRubrics'], $columns[4]);
					if(isset($columns[5]) and $columns[5] != '') $curriculum['assessment'.$columns[0].'LO'] = explode(', ', $columns[5]);
					if(isset($columns[6]) and $columns[6] != '')$curriculum['gradAttr'.$columns[0]] = explode(', ', $columns[6]);
					break;
				case 'Formative Feedback':
					if($columns[0] == null) break;
					$curriculum['formativeFeedback'] = $columns[0];
					break;
				case 'Approach':
					if($columns[0] == null) break;
					else if($columns[0] == 'S/N') break;
					array_push($curriculum['approachMain'], $columns[1]);
					array_push($curriculum['approachDescription'], $columns[2]);
					break;
				case 'References':
					if($columns[0] == null) break;
					else if($columns[0] == 'S/N') break;
					array_push($curriculum['references'], $columns[1]);
					break;
				case 'Course Instructors':
					if($columns[0] == null) break;
					else if($columns[0] == 'ID') break;
					array_push($curriculum[$columns[0]], $columns[1]);
					break;
				case 'Schedule':
					if($columns[0] == null) break;
					else if($columns[0] == 'Week') break;
					array_push($curriculum['scheduleWeek'], $columns[0]);
					array_push($curriculum['scheduleTopic'], $columns[1]);
					array_push($curriculum['scheduleReadings'], $columns[2]);
					array_push($curriculum['scheduleActivities'], $columns[3]);
					if(isset($columns[4]) and $columns[4] != '') $curriculum['scheduleLO'.$columns[0]] = explode(', ', $columns[4]);
					break;
				case 'Appendix':
					if($columns[0] == null) break;
					else if($columns[0] == 'ID') break;
					array_push($curriculum['appendixHeader'], $columns[1]);
					array_push($curriculum['appendixDescription'], $columns[2]);
					break;
				case 'Criteria':
					if($columns[0] == null) break;
					else if($columns[0] == 'ID') break;

					if($columns[0] == 'appendixID' && !isset($curriculum['assessmentCriteria'.$columns[1]])){
						if($currentAppendixID != (int)$columns[1]){
							$currentAppendixID = (int)$columns[1];
							$curriculum['assessmentCriteria'.$columns[1]] = [];
							$curriculum['assessmentFail'.$columns[1]] = [];
							$curriculum['assessmentPass'.$columns[1]] = [];
							$curriculum['assessmentHigh'.$columns[1]] = [];
							break;
						}
						break;
					}
					else if($columns[0] == 'header'){
						array_push($curriculum['assessmentCriteria'.$currentAppendixID], $columns[1]);
					}
					else if($columns[0] != 'header' && $columns[0] != 'appendixID'){
						array_push($curriculum['assessment'.ucfirst($columns[0]).$currentAppendixID], $columns[1]);
					}

					break;
			}
		}
		fclose($file);
		// echo json_encode($curriculum, JSON_PRETTY_PRINT);
		// die;

		// Implode Array to string
		$curriculum['prerequisite'] = implode(', ', $curriculum['prerequisite']);

		//ContactHour
		// var_dump($contactType);
		// var_dump($contactHour);
		
		foreach ($contactType as $i => $type ){
			$curriculum['chAtt'.($i+1)] = $type;
			$curriculum['chInput'.($i+1)] = $contactHour[$i];
		}

		// echo json_encode($curriculum, JSON_PRETTY_PRINT);
		// die;

		$c = new Curriculum();
		$c->updateCurriculum($curriculum);
	}
}
?>

<title>Import - FYP</title>
<body>
	<div class="container" id="curriculum">
		<div id="header">
			<h2>Import Curriculum</h2>
		</div>
		<?php include 'includes/links.inc.php'?>
		<div id="greeting">
			<p>Hello, <?php echo $user->email;?></p>
		</div>
		<div id="import" class="content">
			<form id="form" action="import.php" method="post" enctype="multipart/form-data">
				<input id="file" type="file" name="file" accept=".csv"/>
				<input type="submit" value="Import Curr" name="submit"/>
			</form>
		</div>
	</div>
</body>
</html>
