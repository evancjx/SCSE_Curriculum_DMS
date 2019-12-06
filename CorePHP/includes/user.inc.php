<?php
//If there is no constant defined caled __CONFIG___, do not load this file
if(!defined('__CONFIG__')){
  exit('You do not have the config file');
  //404 page
}

class User{
  private $con;

  public $user_id;

  public function __construct(int $user_id){
    $this->con = DB::getConnection();

    $user_id = Filter::Int($user_id);

    $user = $this->con->prepare("SELECT user_id, email, reg_time FROM users WHERE user_id=:user_id LIMIT 1");
    $user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $user->execute();
    if($user->rowCount() == 1){
      $user = $user->fetch(PDO::FETCH_OBJ);

      $this->email    = (string) $user->email;
      $this->user_id  = (int) $user->user_id;
      $this->reg_time = (string) $user->reg_time;
    }
    else{
      //no User
      header("Location: /logout.php"); exit;
    }
  }

  public static function Find($email, $return_assoc = false){
    $con = DB::getConnection();

    $email = (string) Filter::String($email);
    $sql = $con->prepare("SELECT user_id, password FROM users where email=LOWER(:email) LIMIT 1");
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->execute();

    if($return_assoc)
      return $sql->fetch(PDO::FETCH_ASSOC);
    else
      return (boolean) $sql->rowCount();
  }
}
?>
