<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\UserService;
use vagrant\TheBoringSocial\php\class\FollowService;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/friends.html");

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
    $followService = new FollowService($servername, $username, $password);

    $friends = "";

    $logger = new Logger('Friends');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $user = $userService->catchUserData($_SESSION["user"]);
    $logger->info(sprintf('Utente %s si trova nella sezione friends', $user->getUsername()));


    $followers = count($followService->getAllFollowers($user->getId()));
    $follows = $followService->getAllMyFollow($user->getId());

    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " " . $user->getSurname(), $html);
    $html = str_replace("%followers%", $followers, $html);
    $html = str_replace("%counterFollow%", count($follows), $html);


    if (isset($_POST["searchSubmit"])) {
        if (empty($_POST["search"])) {
            header("Location: searchProfile.php");
            die();
        } else {
            header(sprintf("Location: searchProfile.php?username=%s", $_POST["search"]));
            die;
        }
    }


    foreach ($follows as $follow) {

        $followData = $userService->catchUserDataWithId($follow->getFollow_user_id());

        $friends = $friends . sprintf('
        <div class="col-md-6 m-b-2">
            <div class="p-10 bg-white">
                <div class="media media-xs overflow-visible">
                    <a class="media-left" href="profile.php?username=%s">
                    <img src="%s" alt="" class="media-object img-circle" >
                    </a>
                    <div class="media-body valign-middle">
                        
                    <a class="media-left" href="profile.php?username=%s"><b class="text-inverse">%s</b></a>
                        
                    </div>
                </div>
            </div>
        </div>
        ', $followData->getUsername(), $followData->getImagePath(), $followData->getUsername(), $followData->getName() . "" . $followData->getSurname());

        $followData = "";
    }

    $html = str_replace("<!-- Follow -->", $friends, $html);

    echo $html;
} catch (PDOException $e) {
    $logger->error($e->getMessage());
    echo "Connection failed: " . $e->getMessage();
}
