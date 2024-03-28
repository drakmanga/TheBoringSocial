<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\UserService;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/moreInformationProfile.html");


session_start();

if (empty($_SESSION["user"])) {
    header("Location: login.php");
    die();
}
if (isset($_POST["logout"])) {
    $_SESSION = array();
    Logout::logout();
}

$servername = "localhost";
$username = "root";
$password = "exercise";

try {



    $userService = new UserService($servername, $username, $password);
    $user = $userService->catchUserData($_SESSION["user"]);

    $logger = new Logger('More Info Profile');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());



    $userInfo = $userService->catchUserData($_GET["username"]);


    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);

    $html = str_replace("%usernameUser%", $userInfo->getUsername(), $html);
    $html = str_replace("%imageProfileUser%", $userInfo->getImagePath(), $html);
    $html = str_replace("%nameAndSurname%", $userInfo->getName() . " " . $userInfo->getSurname(), $html);
    $html = str_replace("%description%", $userInfo->getDescription(), $html);
    $html = str_replace("%city%", $userInfo->getCity(), $html);
    $html = str_replace("%gender%", $userInfo->getGender(), $html);
    $html = str_replace("%birthday%", $userInfo->getBirthday(), $html);
    $html = str_replace("%language%", $userInfo->getLanguage(), $html);
    $html = str_replace("%name%", $userInfo->getName(), $html);
    $html = str_replace("%surname%", $userInfo->getSurname(), $html);
    $html = str_replace("%webpage%", $userInfo->getWebPage(), $html);

    $html = str_replace("<!-- usernameUser -->", $userInfo->getUsername(), $html);
    $html = str_replace("%usernameUser%", $userInfo->getUsername(), $html);
    $html = str_replace("<!-- nameAndSurnameUser -->", $userInfo->getName() . " " . $userInfo->getSurname(), $html);

    echo $html;
} catch (PDOException $e) {
    $logger->error($e->getMessage());
    echo "Connection failed: " . $e->getMessage();
}
