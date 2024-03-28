<?php

namespace vagrant\TheBoringSocial\php\class;


class Password
{

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

    public static function checkAndPrintErrorPassword($password)
    {
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[@\#\%\&\?\!\/]/', $password);
        $lenghtPassword = strlen($password) < 8;

        $errors = [$uppercase, $lowercase, $number, $specialChars, $lenghtPassword];

        if ($errors[0] == 0) {
            echo "<div class=text-center mt-4'><p class='text-danger'>la password deve contenere almeno una lettera maiuscola </p> </div> ";
        }
        if ($errors[1] == 0) {
            echo "<div class=text-center mt-4'><p class='text-danger'>la password deve contenere almeno una lettera minuscola </p> </div>";
        }
        if ($errors[2] == 0) {
            echo "<div class=text-center mt-4'><p class='text-danger'>la password deve contenere almeno un numero </p> </div> ";
        }
        if ($errors[3] == 0) {
            echo "<div class=text-center mt-4'><p class='text-danger'>la password deve contenere almeno un simbolo fra questi: @#%&?!/  </p> </div>";
        }
        if ($errors[4]) {
            echo "<div class=text-center mt-4'><p class='text-danger'>la password deve essere lunga almeno 8 caratteri </p> </div> ";
        }

        if ($errors[0] == 1 && $errors[1] == 1 && $errors[2] == 1 && $errors[3] == 1 && $errors[4] == false) {
            return $password;
        } else die;
    }

    public static function cryptPswd($pswd)
    {
        $cryptedPswd = password_hash($pswd, PASSWORD_BCRYPT);
        return $cryptedPswd;
    }

    public static function matchPswd($pswd, $dbPswd)
    {

        $matchedPswd = password_verify($pswd, $dbPswd,);
        return $matchedPswd;
    }

    public static function generateNewTemporaryPassword()
    {

        $lower = str_split("abcdefghijklmonpqrstuvwxyz");
        $upper = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $symbols = str_split("@#%&?!/");
        $number = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

        $arrayPassword = [];
        $character = array(
            $lower,
            $upper,
            $number,
            $symbols
        );

        $passwordLenght = 16;

        for ($counter = 0; $counter < $passwordLenght; $counter++) {
            $x = rand(0, 3);
            $maxY = count($character[$x]) - 1;
            $y = rand(0, $maxY);
            $arrayPassword[$counter] = $character[$x][$y];
        }

        $passwordgenerated = implode($arrayPassword);

        return $passwordgenerated;
    }

    public static function changeOrPrintErrorPassword($password)
    {
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[@\#\%\&\?\!\/]/', $password);
        $lenghtPassword = strlen($password) < 8;


        $errors = [$uppercase, $lowercase, $number, $specialChars, $lenghtPassword];

        if ($errors[0] == 0) {
            $error1 = "<div class=container 'mt-3'><p class='text-danger'> la password deve contenere almeno una lettera maiuscola</p></div>";
        } else $error1 = "";
        if ($errors[1] == 0) {
            $error2 = "<div class=container 'mt-3'><p class='text-danger'> la password deve contenere almeno una lettera minuscola</p></div>";
        } else $error2 = "";
        if ($errors[2] == 0) {
            $error3 = "<div class=container 'mt-3'><p class='text-danger'> la password deve contenere almeno un numero</p></div>";
        } else $error3 = "";
        if ($errors[3] == 0) {
            $error4 = "<div class=container 'mt-3'><p class='text-danger'> la password deve contenere almeno  un simbolo fra questi: @#%&?!/</p></div>";
        } else $error4 = "";
        if ($errors[4]) {
            $error5 = "<div class=container 'mt-3'><p class='text-danger'> la password deve deve essere lunga almeno 8 caratteri</p></div>";
        } else $error5 = "";

        if ($errors[0] == 1 && $errors[1] == 1 && $errors[2] == 1 && $errors[3] == 1 && $errors[4] == false) {
            return $password;
        } else {
            return  $password = $error1 . $error2 . $error3 . $error4 . $error5;
        }
    }
}
