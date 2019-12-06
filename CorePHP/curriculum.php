<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';

Page::ForceLogin();

if(!isset($_GET['function'])) header('Location: /');

include 'includes/header.php';

$c = new Curriculum();
$data = $c->controllerCurriculum($_SERVER);
$gradAttr = $c->getGraduateAttribute();

$function = strtolower($_GET['function']);
?>
<body>
	<div class="container" id="curriculum">
		<div id="header">
			<h2><?php echo ucfirst($function); ?> Curriculum</h2>
		</div>
		<?php include 'includes/links.inc.php'?>
	  <form id="curriculumForm" class="curriculum" method="post" action="query.php">
			<div id='submit'>
				<input type='hidden' name='function' value='<?php echo strtolower($_GET['function']) ?>'/>
				<button type='submit' id='submit' name='submit'>Submit</td></tr>
			</div>
			<?php
			include 'includes/sectors/courseMainDetails.sec.inc.php';
		 	include 'includes/sectors/objectives.sec.inc.php';
			include 'includes/sectors/content.sec.inc.php';
			include 'includes/sectors/assessment.sec.inc.php';
			include 'includes/sectors/mapGraduateAttributes2.sec.inc.php';
		 	include 'includes/sectors/formativeFeedback.sec.inc.php';
			include 'includes/sectors/LearningAndTeachingApproach.sec.inc.php';
			include 'includes/sectors/ReadingAndReferences.sec.inc.php';
			include 'includes/sectors/CourseInstructors.sec.inc.php';
			include 'includes/sectors/plannedWeeklySchedule.sec.inc.php';
			include 'includes/sectors/appendix.sec.inc.php';
			?>
	  </form>
	</div>
	<script src='/assets/js/main.js'></script>
</body>
</html>
