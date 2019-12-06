<div id="links">
    <!-- <a href="/">Home</a> -->
    <?php
    if(isset($_SESSION['user_id'])){
      $links['Home'] = 'home.php';
      $links['Search'] = 'search.php';
      $links['Import'] = 'import.php';
      $links['Insert'] = 'curriculum.php?function=insert';
      $links['Logout'] = 'logout.php';
    }
    else{
      $links['Home'] = '';
      $links['Search'] = 'search.php';
      $links['Register'] = 'register.php';
      $links['Login'] = 'login.php';
    }
    $display = "<ul id='link'>";
    foreach($links as $key => $link){
      $display.= "<a href='/".$link."'><li>".$key."</li></a>";
    }
    $display.= "</ul>";
    echo $display;
    ?>
</div>
