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
  $password = $_POST['password'];

  //Make sure the user does not exist
  $user_found = User::Find($email, true);

  if($user_found){
    //User exists, try and sign them in
    //We can also check to see if they are able to log in.
    $user_id = (int) $user_found['user_id'];
    $hash = (string) $user_found['password'];

    if(password_verify($password, $hash)){
      //User is signed in
      $return['redirect'] = '../dashboard.php';
      $return['is_logged_in'] = true;
      $_SESSION['user_id'] = $user_id;
      unset($_SESSION['user_email']);
    }
    else{
      //Invalid user email/password combo
      $return['error'] = "Invalid user email/password combo";
      $return['is_logged_in'] = false;
    }
  }
  else {
    //User does not exist, they need to create a new account
    $return['error'] = "You do not have an account. <a href='/register.php'>Create one now</a>";
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
