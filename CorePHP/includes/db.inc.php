<?php
//If there is no constant defined caled __CONFIG___, do not load this file
if(!defined('__CONFIG__')){
  exit('You do not have the config file');
  //404 page
}

class DB{
  private $servername;
  private $username;
  private $password;
  private $dbname;
  private $charset;

  protected static $pdo;

  protected function __construct(){
    $this->servername = "localhost";
    $this->username = "HeM2HntGFy";
    $this->password = "eHQr.5@'%c3Aq>vRB6ww";
    $this->dbname = "administration";
    $this->charset = "utf8mb4";

    try{
      $dsn = "mysql:host=".$this->servername.";dbname=".$this->dbname.";charset=".$this->charset;
      self::$pdo = new PDO($dsn, $this->username, $this->password);
      self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

      // $this->pdo = $pdo;
    } catch (PDOException $e){
      echo "Connection fail: ";
      echo "<br/><br/><b>Failed</b>: ".$e->getMessage()."<br/>";
      echo "<br/>".$e->getTraceAsString()."<br/>";
      foreach ($e->getTrace() as $key => $row)
          echo "<br/>" . $row['file'] . " (" . $row['line'] . ")<br/>";
      exit;
    }
  }

  public static function getConnection(){
    if(!self::$pdo){
      new DB();
    }
    return self::$pdo;
  }
}

?>
