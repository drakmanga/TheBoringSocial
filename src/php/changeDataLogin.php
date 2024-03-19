<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\UserService;

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
    $userService = new UserService($servername,$username,$password);
    $user = $userService->catchUserData($_SESSION["user"]);

    $logger = new Logger('Change Data Login');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    if (!empty($_POST["username"])) {
        
        if(!empty($usernameCheck = $userService->changeUsernameAndPrintError($_POST["username"]))) {
            $userService->updateInfoUser($user->getUsername(), "username", $usernameCheck);
            $_SESSION["user"] = $usernameCheck;
            $logger->info(sprintf('Utente %s ha modificato il suo username', $user->getUsername()));
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
                    $userService->updateInfoUser($user->getUsername(), "password", $cryptPswd);
                    $logger->info(sprintf('Utente %s ha modificato la sua password', $user->getUsername()));
                    $html = str_replace("<!-- password cambiata -->", "<div class=container 'mt-3'><p class='text-success'> password cambiata con successo</p></div>" , $html);
                    
                }
            }
        }
    }

    $user = $userService->catchUserData($_SESSION["user"]);
    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    echo $html;

} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}
