<?php
namespace vagrant\TheBoringSocial\class;


class Password {
  
private $password;

public function getPassword()
{
    return $this->password;
}

public function setPassword($password)
{
    $this->password = $password;

    return $this;
}

public static function checkAndPrintErrorPassword ($password) {
    $uppercase = preg_match('/[A-Z]/', $password);
    $lowercase = preg_match('/[a-z]/', $password);
    $number = preg_match('/[0-9]/', $password);
    $specialChars = preg_match('/[@\#\%\&\?\!\/]/', $password);
    $lenghtPassword= strlen($password) < 8;
    
    $errors = [$uppercase, $lowercase, $number, $specialChars, $lenghtPassword];
    
    if($errors[0]== 0) {
        echo "<div class=container 'mt-3'><p class='text-danger'>la password deve contenere almeno una lettera maiuscola </p> </div> ";
    }
    if($errors[1]== 0) {
        echo "<div class=container 'mt-3'><p class='text-danger'>la password deve contenere almeno una lettera minuscola </p> </div>";
    }
    if($password[2]== 0) {
        echo "<div class=container 'mt-3'><p class='text-danger'>la password deve contenere almeno un numero </p> </div> ";
    }
    if($errors[3]== 0) {
        echo "<div class=container 'mt-3'><p class='text-danger'>la password deve contenere almeno un simbolo fra questi: @#%&?!/  </p> </div>";
    }
    if($errors[4]) {
        echo "<div class=container 'mt-3'><p class='text-danger'>la password deve essere lunga almeno 8 caratteri </p> </div> ";
    }

    if ($errors[0]==1 && $errors[1]== 1 && $errors[2]== 1 && $errors[3]== 1 && $errors[4]== false) {
        return $password;
    } else die;   
}

public static function cryptPswd($pswd) {
    $cryptedPswd = password_hash($pswd, PASSWORD_BCRYPT);
    return $cryptedPswd;
}

public static function matchPswd($pswd,$dbPswd){
    
    $matchedPswd = password_verify($pswd,$dbPswd,);
    return $matchedPswd;
}

}