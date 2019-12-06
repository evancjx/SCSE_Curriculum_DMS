<?php
//If there is no constant defined caled __CONFIG___, do not load this file
if(!defined('__CONFIG__')){
  exit('You do not have the config file');
  //404 page
}

//Sessions are always turn on
if(!isset($_SESSION)){
  session_start();
}

//Our config is below
// Allow errors
error_reporting(-1);
ini_set('display_errors', 'On');

//Include the DB.php file
include_once "db.inc.php";
include_once "user.inc.php";
include_once "page.inc.php";
include_once "filter.inc.php";

$con = DB::getConnection();
?>
