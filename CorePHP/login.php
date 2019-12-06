<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once 'includes/config.inc.php';

Page::ForceDashboard();

include 'includes/header.php';
?>
<title>Login - FYP</title>
<body>
  <div class="container" id='curriculum'>
    <div id="header">
      <h2>SCSE Curriculum Database (Store, Track & Manage)</h2>
    </div>
    <?php include 'includes/links.inc.php'?>
    <div id="greeting">
      <h3>Login</h3>
			<p>Hello, </p>
		</div>
    <form id='form' class='user js-login'>
      <div>
        <input class='' id='tfEmail' type='email' required='required' placeholder="email" value="<?php if(!empty($_SESSION['user_email'])) echo $_SESSION['user_email']; ?>">
      </div>
      <div>
        <input class='' id='tfPassword' type='password' required='required' placeholder="password">
      </div>
      <div class='js-error' style="display: none;"></div>
      <div>
        <button type='submit'>Login</button>
      </div>
    </form>
  </div>
</body>
</html>
<script src='/assets/js/main.js'></script>
