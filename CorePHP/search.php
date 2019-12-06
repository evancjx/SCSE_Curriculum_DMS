<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';

Page::ForceLogin();

$user = new User($_SESSION['user_id']);

include 'includes/header.php';

if (isset($_POST['submit']) && $_POST['function'] == 'search') {
	$c = new Curriculum();
	$result = $c->searchCurriculum($_POST);
}
?>
<title>Search - FYP</title>
<body>
	<div class="container" id='curriculum'>
		<div id="header">
			<h2>Curriculum List</h2>
		</div>
		<?php include 'includes/links.inc.php'?>
		<div id="greeting">
			<p>Hello, <?php echo $user->email;?></p>
		</div>
		<?php include 'includes/sectors/search.sec.inc.php'; ?>
		<div id='result'>
			<?php
				if(isset($result)) utilities::displaySearchResult($result);
				// else{
				// 	echo '<table id="curriculumList" class="form">';
				// 	$c->getCourseList();
				// 	echo '</table>';
				// }
			?>
		</div>
	</div>
	<script src='/assets/js/main.js'></script>
</body>
</html>
