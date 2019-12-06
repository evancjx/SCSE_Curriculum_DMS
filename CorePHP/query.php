<?php
// include 'includes/page.inc.php';
//
// Page:: ForceLogin();

include 'includes/dbh.inc.php';
include 'includes/curriculum.inc.php';
try{
  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // if(empty($_POST['courseCode']) || empty($_POST['courseTitle']) ||
    //   empty($_POST['noOfAU']) || empty($_POST['chAtt1']) ||
    //   empty($_POST['chAtt2']) || empty($_POST['chAtt3'])){
    //   header('Location: /curriculum.php?function=insert');
    // }
    // else if($_POST['chAtt1'] == $_POST['chAtt2'] ||
    //   $_POST['chAtt2'] == $_POST['chAtt3'] ||
    //   $_POST['chAtt3'] == $_POST['chAtt1']){
    //   echo 'Same Field Selected<br>';
    //   //header('Location: /error.php');
    // }
    // else if(!isset($_POST['function'])||empty($_POST['function'])){
    //   echo 'Function error<br>';
    //   //header('Location: /error.php');
    // }
    // else{
      $c = new Curriculum();
      switch(strtolower($_POST['function'])){
        case 'insert':
          if(isset($_POST['file'])){
            echo $filename=$_FILES["file"]["tmp_name"];
            $CSVfp = fopen($_POST['file'], "r");
            if($CSVfp !== FALSE) {
             while(! feof($CSVfp)) {
              $data = fgetcsv($CSVfp, 1000, ",");
              // print_r($data);
             }
            }
            fclose($CSVfp);
            // die();
          }
          else{
            $c->insertCurriculum($_POST);
          }
          break;
        case 'update':
          $c->updateCurriculum($_POST);
          break;
        default:
          die("Unknown function");
          break;
      }
    // }
  }
  else if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if (isset($_GET['delete']) && isset($_GET['code']) && $_GET['delete'] == 'delte'){
      $c = new Curriculum();
      $c->deleteCurriculum($_GET['code']);
    }
  }
  // header('Location: /index.php');
}catch(Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<br/><br/><b>Failed</b>: ".$e->getMessage()."<br/>";
    echo "<br/>".$e->getTraceAsString()."<br/>";
    foreach ($e->getTrace() as $key => $row) {
        echo "<br/>" . $row['file'] . " (" . $row['line'] . ")<br/>";
    }
}
?>
