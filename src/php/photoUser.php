<?php


use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\FileService;
use vagrant\TheBoringSocial\php\class\UserService;
use vagrant\TheBoringSocial\php\class\SearchProfileService;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/photoUser.html");

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

date_default_timezone_set('Europe/Rome');

try {

    $fileService = new FileService($servername, $username, $password);
    $userService = new UserService($servername, $username, $password);
    $searchProfileService = new SearchProfileService($servername, $username, $password);
    $photos = "";

    $logger = new Logger('Photo user');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $user = $userService->catchUserData($_SESSION["user"]);
    $userProfile = $searchProfileService->catchUserFromParameters($_GET["username"]);
    $logger->info(sprintf('Utente %s si trova nella sezione MyPhoto di %s', $user->getUsername(), $_GET["username"]));

    $html = str_replace("<!-- usernameUser -->", $userProfile->getUsername(), $html);
    $html = str_replace("%usernameUser%", $userProfile->getUsername(), $html);
    $html = str_replace("<!-- nameAndSurnameUser -->", $userProfile->getName() . " " . $userProfile->getSurname(), $html);


    if (!empty($_GET["username"])) {
        $userInfoPhoto = $userService->catchUserData($_GET["username"]);
        $filePost = $fileService->catchAllPhotoFromId($userInfoPhoto->getId());
    } else {
        $filePost = $fileService->catchAllPhotoFromId($user->getId());
    }


    foreach ($filePost as $photo) {

        $photos = $photos . sprintf('
        
        <li>
            <a href="../php/commentPage.php?post_id=%s"><img src="%s" alt="" class="img-portrait myclassdrak" /></a>
        </li>', $photo->getPost_id(), $photo->getPath());
    }

    $html = str_replace("%imageProfileUser%", $userProfile->getImagePath(), $html);
    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " " . $user->getSurname(), $html);
    $html = str_replace("<!-- counter -->", count($filePost), $html);

    $html = str_replace("<!-- photo -->", $photos, $html);
    echo $html;
} catch (PDOException $e) {
    $logger->error($e->getMessage());
    echo "Connection failed: " . $e->getMessage();
}
