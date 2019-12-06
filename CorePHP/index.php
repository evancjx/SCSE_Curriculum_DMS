<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';

Page::ForceDashboard();

include 'includes/header.php';
?>
<title>Homepage - FYP</title>
<body>
  <div class="container">
    <div id="header">
      <h2>SCSE Curriculum Database (Store, Track & Manage)</h2>
    </div>
		<?php include 'includes/links.inc.php'?>
	</div>
</body>
</html>
