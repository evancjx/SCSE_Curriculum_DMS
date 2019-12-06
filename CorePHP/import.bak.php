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
			'Learning Outcomes',
			'Course\'s Content Attribute',
			'Content',
			'Assessment',
			'Assessment Learning Outcome Tested',
			'Assessment to Graduate Attributes Mapping',
			'Learning Outcomes to Graduate Attributes Mapping',
			'Formative Feedback',
			'Approach',
			'References',
			'Course Instructors',
			'Schedule',
			'Appendix',
			'Criteria'
		];
		$curriculum = [];
		$tempSection = [];
		while(($columns = fgetcsv($file, 10000, ",")) !== FALSE){
			// var_dump($columns);
			// echo '</br>';
			// echo $columns[0].'</br>';

			if($columns[0] != '' && in_array($columns[0], $sections)){
				$currentSection = $columns[0];
				continue;
			}
			else if($columns[0] == ''){
				// echo $columns[0].'</br>';
				switch ($currentSection) {
					case 'Course Main Details':
						$section = 'courseMainDetails';
						break;
					case 'Pre-requisite':
						$section = 'prerequisite';
						break;
					case 'Learning Outcomes':
						$section = 'learning_outcomes';
						break;
					case 'Course\'s Content Attribute':
						$section = 'cosContentAtt';
						break;
					case 'Content':
						$section = 'content';
						break;
					case 'Assessment':
						$section = 'assessment';
						break;
					case 'Assessment Learning Outcome Tested':
						$section = 'assessmentLOTested';
						break;
					case 'Assessment to Graduate Attributes Mapping':
						$section = 'assessmentGradAttr';
						break;
					case 'Learning Outcomes to Graduate Attributes Mapping':
						$section = 'mapCosLOGradAttr';
						break;
					case 'Formative Feedback':
						$section = 'formativeFeedback';
						break;
					case 'Approach':
						$section = 'approach';
						break;
					case 'References':
						$section = 'reference';
						break;
					case 'Course Instructors':
						$section = 'courseInstructors';
						break;
					case 'Schedule':
						$section = 'schedule';
						break;
					case 'Appendix':
						$section = 'appendix';
						break;
					case 'Criteria':
						$section = 'criteria';
						break;
				}

				$curriculum[$section] = $tempSection;
				if ($section == 'criteria'){
					'here';
					die();
				}
				$tempSection = [];
				$row = [];
				continue;
			}
			// echo $currentSection;
			switch ($currentSection) {
				case 'Course Main Details':
					$tempSection[$columns[0]] = $columns[1];
					// var_dump($tempSection);
					break;
				case 'Pre-requisite':
					// echo 'Pre-requisitie'.'</br>';
					break;
				case 'Learning Outcomes':
					if($columns[0] == 'S/N') break;
					$tempSection[$columns[0]] = $columns[1];
					break;
				case 'Course\'s Content Attribute':
					$tempSection[$columns[0]] = $columns[1];
					break;
				case 'Content':
					if($columns[0] == 'S/N'){
						$tempSection = $row = [];
						break;
					}
					$row['topic'] = $columns[1];
					$row['details1'] = $columns[2];
					$row['details2'] = $columns[3];
					$row['rowspan'] = $columns[4];
					$tempSection[$columns[0]] = $row;
					break;
				case 'Assessment':
					if($columns[0] == 'S/N'){
						$tempSection = $row = [];
						break;
					}
					$row['component'] = $columns[1];
					$row['weightage'] = $columns[2];
					$row['category'] = $columns[3];
					$row['rubrics'] = $columns[4];
					$tempSection[$columns[0]] = $row;
					break;
				case 'Assessment Learning Outcome Tested':
					if($columns[0] == 'Assessment S/N'){
						$tempSection = $row = [];
						break;
					}
					$tempSection[$columns[0]] = $columns[1];
					break;
				case 'Assessment to Graduate Attributes Mapping':
					if($columns[0] == 'Assessment S/N' || $columns[0] == ''){
						// $tempSection = $row = [];
						break;
					}
					$tempSection[$columns[0]] = $columns[1];
					break;
				case 'Learning Outcomes to Graduate Attributes Mapping':
					if($columns[0] == 'Learning Outcomes S/N'){
						$tempSection = $row = [];
						break;
					}
					$tempSection[$columns[0]] = $columns[1];
					break;
				case 'Formative Feedback':
					$tempSection = $columns[0];
					break;
				case 'Approach':
					if($columns[0] == 'S/N'){
						$tempSection = $row = [];
						break;
					}
					$row['main'] = $columns[1];
					$row['description'] = $columns[2];
					$tempSection[$columns[0]] = $row;
					break;
				case 'References':
					if($columns[0] == 'S/N'){
						$tempSection = $row = [];
						break;
					}
					$tempSection[$columns[0]] = $columns[1];
					break;
				case 'Course Instructors':
					if(!isset($tempSection['name'])){
						$tempSection['name'] = [];
						$tempSection['office'] = [];
						$tempSection['phone'] = [];
						$tempSection['email'] = [];
					}
					if($columns[0] == 'ID'){
						$tempID = $columns[1];
					}
					else{
						switch ($columns[0]) {
							case 'instructorName':
								array_push($tempSection['name'], $columns[1]);
								break;
							case 'instructorOffice':
								array_push($tempSection['office'], $columns[1]);
								break;
							case 'instructorPhone':
								array_push($tempSection['phone'], $columns[1]);
								break;
							case 'instructorEmail':
								array_push($tempSection['email'], $columns[1]);
								break;
						}
					}
					break;
				case 'Schedule':
					if($columns[0] == 'Week'){
						$tempSection = $row = [];
						break;
					}
					$row['topic'] = $columns[1];
					$row['readings'] = $columns[2];
					$row['activities'] = $columns[3];
					$row['loIDs'] = $columns[4];
					$tempSection[$columns[0]] = $row;
					break;
				case 'Appendix':
					if($columns[0] == 'ID'){
						$tempSection = $row = [];
						break;
					}
					$row['header'] = $columns[1];
					$row['description'] = $columns[2];
					$tempSection[$columns[0]] = $row;
					break;
				case 'Criteria':
					switch ($columns[0]) {
						case 'appendixID':
							$row['appendixID'] = $columns[1];
							break;
						case 'ID':
							$row['ID'] = $columns[1];
							break;
						case 'header':
							$row['header'] = $columns[1];
							break;
						case 'fail':
							$row['fail'] = $columns[1];
							break;
						case 'pass':
							$row['pass'] = $columns[1];
							break;
						case 'high':
							$row['high'] = $columns[1];
							echo 'here';
							$tempSection[] = [
			          'appendixID' => $row['appendixID'],
			          'ID' => $row['ID'],
			          'header' => $row['header'],
			          'fail' => $row['fail'],
			          'pass' => $row['pass'],
			          'high' => $row['high']
			        ];
							var_dump($tempSection);
							break;
					}
					break;
				default:
					// code...
					break;
			}
		}
		fclose($file);

		foreach($curriculum['courseMainDetails'] as $header => $details){
			$curriculum[$header] = $details;
		}
		unset($curriculum['courseMainDetails']);
		$curriculum['courseCode'] = str_replace("\\", "", $curriculum['courseCode']);

		foreach($curriculum['learning_outcomes'] as $details){
			$temp[] = $details;
		}
		$curriculum['LO'] = $temp;
		unset($curriculum['learning_outcomes']);

		foreach($curriculum['cosContentAtt'] as $header => $details){
			if($header == 'att1') $header = 'cosContentAtt1';
			else if($header == 'att2') $header = 'cosContentAtt2';
			$curriculum[$header] = $details;
		}
		unset($curriculum['cosContentAtt']);

		$temp = [];
		foreach($curriculum['content'] as $header => $details){
			array_push($temp, (string)$header);
		}
		$curriculum['ID'] = $temp;
		$temp = [];
		foreach($curriculum['content'] as $header => $details){
			$temp[$header] = $details['topic'];
		}
		$curriculum['topics'] = $temp;
		foreach($curriculum['content'] as $header => $details){
			$temp[$header] = $details['details1'];
		}
		$curriculum['cosContentDetails1'] = $temp;
		foreach($curriculum['content'] as $header => $details){
			$temp[$header] = $details['details2'];
		}
		$curriculum['cosContentDetails2'] = $temp;
		$temp = [];
		foreach($curriculum['content'] as $header => $details){
			if($details['rowspan'] == '2'){
				array_push($temp, (string)$header);
			}
		}
		$curriculum['merge'] = $temp;
		unset($curriculum['content']);

		$temp = [];
		foreach($curriculum['assessment'] as $header => $details){
			array_push($temp, $details['component']);
		}
		$curriculum['component'] = $temp;
		$temp = [];
		foreach($curriculum['assessment'] as $header => $details){
			array_push($temp, $details['weightage']);
		}
		$curriculum['weightage'] = $temp;
		$temp = [];
		foreach($curriculum['assessment'] as $header => $details){
			$curriculum['componentCat'.$header] = $details['category'];
		}
		$temp = [];
		foreach($curriculum['assessment'] as $header => $details){
			array_push($temp, $details['rubrics']);
		}
		$curriculum['assessmentRubrics'] = $temp;
		unset($curriculum['assessment']);

		$temp = [];
		foreach($curriculum['assessmentLOTested'] as $header => $details){
			$curriculum['assessment'.$header.'LO'] = explode(', ', $details);
		}
		unset($curriculum['assessmentLOTested']);

		$temp = [];
		foreach($curriculum['assessmentGradAttr'] as $header => $details){
			$curriculum['gradAttr'.$header] = explode(', ', $details);
		}
		unset($curriculum['assessmentGradAttr']);

		$temp = [];
		foreach($curriculum['mapCosLOGradAttr'] as $header => $details){
			$curriculum['LOgradAttr'.$header] = explode(', ', $details);
		}
		unset($curriculum['mapCosLOGradAttr']);

		//approach
		$main = $description = [];
		foreach($curriculum['approach'] as $header => $details){
			array_push($main, $details['main']);
			array_push($description, $details['description']);
		}
		$curriculum['approachMain'] = $main;
		$curriculum['approachDescription'] = $description;
		unset($main); unset($description);
		unset($curriculum['approach']);

		//References
		$references = [];
		foreach($curriculum['reference'] as $header => $details){
			array_push($references, $details);
		}
		$curriculum['references'] = $references;
		unset($references);
		unset($curriculum['reference']);

		//Course Instructors
		$curriculum['instructorName'] = $curriculum['courseInstructors']['name'];
		$curriculum['instructorOffice'] = $curriculum['courseInstructors']['office'];
		$curriculum['instructorPhone'] = $curriculum['courseInstructors']['phone'];
		$curriculum['instructorEmail'] = $curriculum['courseInstructors']['email'];
		unset($curriculum['courseInstructors']);

		//Schedule
		$week = $topic = $readings = $activities = [];
		foreach($curriculum['schedule'] as $header => $details){
			array_push($week, $header);
			array_push($topic, $details['topic']);
			array_push($readings, $details['readings']);
			array_push($activities, $details['activities']);
			$curriculum['scheduleLO'.$header] = explode(', ', $details['loIDs']);
		}
		$curriculum['scheduleWeek'] = $week;
		$curriculum['scheduleTopic'] = $topic;
		$curriculum['scheduleReadings'] = $readings;
		$curriculum['scheduleActivities'] = $activities;
		unset($week); unset($topic); unset($readings); unset($activities);
		unset($curriculum['schedule']);

		//Appendix
		$appendixHeader = $description = [];
		foreach($curriculum['appendix'] as $header => $details){
			array_push($appendixHeader, $details['header']);
			array_push($description, $details['description']);
		}
		$curriculum['appendixHeader'] = $appendixHeader;
		$curriculum['appendixDescription'] = $description;
		unset($appendixHeader); unset($description);
		unset($curriculum['appendix']);

		$temp = [];
		// $criteria = $fail = $pass
		foreach($curriculum['criteria'] as $header =>$details){
			if(!isset($temp[$details['appendixID']])){
				$temp[$details['appendixID']]['criteria'] = [];
				$temp[$details['appendixID']]['fail'] = [];
				$temp[$details['appendixID']]['pass'] = [];
				$temp[$details['appendixID']]['high'] = [];
			}

			array_push($temp[$details['appendixID']]['criteria'], $details['header']);
			array_push($temp[$details['appendixID']]['fail'], $details['fail']);
			array_push($temp[$details['appendixID']]['pass'], $details['pass']);
			array_push($temp[$details['appendixID']]['high'], $details['high']);
		}
		foreach($temp as $header => $details){
			$curriculum['assessmentCriteria'.$header] = $details['criteria'];
			$curriculum['assessmentFail'.$header] = $details['fail'];
			$curriculum['assessmentPass'.$header] = $details['pass'];
			$curriculum['assessmentHigh'.$header] = $details['high'];
		}
		unset($curriculum['criteria']);

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
