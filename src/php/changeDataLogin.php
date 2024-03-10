<?php

use vagrant\TheBoringSocial\php\class\DbFunction;
use vagrant\TheBoringSocial\php\class\Password;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/changeDataLogin.html");


session_start();

if (empty($_SESSION["user"])) {
    header("Location: login.php");
    die();
}

$servername = "localhost";
$username = "root";
$password = "exercise";

try {
    $cryptPswd=null;
    $dbFunction = new DbFunction($servername,$username,$password);
    $user = $dbFunction->catchUserData($_SESSION["user"]);

    if (!empty($_POST["username"])) {
        
        if(!empty($usernameCheck = $dbFunction->changeUsernameAndPrintError($_POST["username"]))) {
            $dbFunction->updateInfoUser($user->getUsername(), "username", $usernameCheck);
            $_SESSION["user"] = $usernameCheck;
        }else {
            $html = str_replace("<!-- username utilizzato -->", "<div class=container 'mt-3'><p class='text-danger'> username già utilizzato </p> </div>" , $html);
        }
    }

    if (!empty($_POST["oldPwd"]) && ($_POST["newPwd"])) {

        if (!Password::matchPswd($_POST["oldPwd"],$user->getPassword())) {
            $html = str_replace("<!-- password vecchia non corretta -->","<div class=container 'mt-3'><p class='text-danger'> vecchia password non corretta</p></div>",$html);
        }

        if (Password::matchPswd($_POST["oldPwd"],$user->getPassword())) {
            $passwordCheckOrPrintError = Password::changeOrPrintErrorPassword($_POST["newPwd"]);
            if ($passwordCheckOrPrintError != ($_POST["newPwd"])) {
                $html = str_replace("<!-- errori nuova password -->", $passwordCheckOrPrintError , $html);
            } else {
                if (Password::matchPswd($_POST["newPwd"],$user->getPassword())) {
                    $html = str_replace("<!-- errori nuova password -->", "<div class=container 'mt-3'><p class='text-danger'> la nuova password non può essere uguale alla vecchia</p></div>" , $html);
                }else {
                    $cryptPswd = Password::cryptPswd($passwordCheckOrPrintError);
                    $dbFunction->updateInfoUser($user->getUsername(), "password", $cryptPswd);
                    $html = str_replace("<!-- password cambiata -->", "<div class=container 'mt-3'><p class='text-success'> password cambiata con successo</p></div>" , $html);
                    
                }
            }
        }
    }

    $user = $dbFunction->catchUserData($_SESSION["user"]);
    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    echo $html;

} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
