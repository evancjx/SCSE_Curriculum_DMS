<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';

Page::ForceLogin();

$user = new User($_SESSION['user_id']);

include 'includes/header.php';
$c = new Curriculum();
$c->controllerCurriculum($_SERVER);
?>
<title>Dashboard - FYP</title>
<body>
	<div class="container" id='curriculum'>
		<div id="header">
			<h2>Curriculum List</h2>
		</div>
		<?php include 'includes/links.inc.php'?>
		<div id="greeting">
			<p>Hello, <?php echo $user->email;?></p>
		</div>
		<table id="curriculumList" class='form'>
			<?php $c->getCourseList() ?>
		</table>
	</div>
</body>
</html>
