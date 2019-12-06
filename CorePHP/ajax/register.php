<?php
//Allow the config
define('__CONFIG__', true);
//Require the config
require_once '../includes/config.inc.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  // Always return JSON format
  header('Content-Type: application/json');

  $return  = [];
  $email = Filter::String($_POST['email']);
  $_SESSION['user_email'] = $email;

  //Make sure the user does not exist
  $user_found = User::Find($email);

  if($user_found){
    //User exists
    //We can also check to see if they are able to log in.
    $return['error'] = "You already have an account. <a href='/login.php'>Login here</a>";
    $return['is_logged_in'] = false;
  }
  else {
    //User does not exist, add them now.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    try{
      $sql = "INSERT INTO users (email, password) VALUES (LOWER(:email), :password)";
      $sql = $con->prepare($sql);
      $sql->bindParam(':email', $email, PDO::PARAM_STR);
      $sql->bindParam(':password', $password, PDO::PARAM_STR);
      $sql->execute();
    }
    catch(Exception $e){
      $this->pdo->rollBack();
      $return['error'] = $e->getTraceAsString();
    }

    $user_id = $con->lastInsertId();
    $_SESSION['user_id'] = (int) $user_id;
    $return['redirect'] = '../dashboard.php?message=Welcome';
    $return['is_logged_in'] = true;
  }

  echo json_encode($return, JSON_PRETTY_PRINT); exit;

  // $array = ['test', 'test2', 'test3', array('name' => 'Test', 'lastname' => 'test')];
  // echo json_encode($array, JSON_PRETTY_PRINT);
}
else{
  //Kill the script
  exit('test');
}
?>
