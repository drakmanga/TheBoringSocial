<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\UserService;

require "../../vendor/autoload.php";
$html = file_get_contents("../html/creationNewPwd.html");
echo $html;

session_start();

if (empty($_SESSION["user"])) {
    header("Location: login.php");
    die();
}

$servername = "localhost";
$username = "root";
$password = "exercise";

try {
    $userService = new UserService($servername, $username, $password);

    $logger = new Logger('Creation New Password');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    if (!empty($_POST["pwd"])) {
        $passwordCheck = Password::checkAndPrintErrorPassword($_POST["pwd"]);
        $cryptPswd = Password::cryptPswd($passwordCheck);
        $userService->updateNewPassword($_SESSION["user"], $cryptPswd);
        $logger->info(sprintf('Utente %s ha modificato la sua password', $user->getUsername()));
        $_SESSION = array();
        Logout::logout();
    }
} catch (PDOException $e) {
    $logger->error($e->getMessage());
    echo "Connection failed: " . $e->getMessage();
}
