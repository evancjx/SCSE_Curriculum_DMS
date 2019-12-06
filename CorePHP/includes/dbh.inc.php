<?php

class Dbh{
  private $servername;
  private $username;
  private $password;
  private $dbname;
  private $charset;

  protected $pdo;

  protected function connect(){
    $this->servername = "localhost";
    $this->username = "admin";
    $this->password = "kj=M3hCxK@T)W9Cn";
    $this->dbname = "curriculum";
    $this->charset = "utf8mb4";

    try{
      $dsn = "mysql:host=".$this->servername.";dbname=".$this->dbname.";charset=".$this->charset;
      $pdo = new PDO($dsn, $this->username, $this->password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $pdo->beginTransaction();

      $this->pdo = $pdo;
      //return $pdo;
    } catch (Exception $e){
      echo "Connection fail: ".$e->getMessage();
    }
  }
}

?>
