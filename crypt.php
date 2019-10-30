<?php
/*
	File: crypt.php
	Author: Jaspers
	Created by 2018-07-11
	Goal: 암호화 알고리즘
	Description:
*/

class Bcrypt{
  
  private $salt;
  
  public function __construct(){
    $this->salt = '$2a$07$R.gJb2U2N.FmZ4hPp1y2CN$';
  }
  
  public function available(){
  
    if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH){
      // echo "CRYPT_BLOWFISH is enabled!";
      return true;
    }
    else{
      // echo "CRYPT_BLOWFISH is not available";  
      return false;
    }
  }
  
  public function decrypt($password){  
    $salt = $this->salt;
    $cryptPasswd = crypt($password, salt);
    
    return $cryptPasswd;
  }

}

?>