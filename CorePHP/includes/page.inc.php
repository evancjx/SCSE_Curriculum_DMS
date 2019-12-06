<?php
class Page{
  //Force the user to login
  static function ForceLogin(){
    if(!isset($_SESSION['user_id'])){
      header("Location: /login.php");
    }
  }
  //Force the user to Home
  static function ForceDashboard(){
    if(isset($_SESSION['user_id'])){
      //The user is allowed here, but redirect
      header("Location: /home.php");
    }
  }
}
?>
