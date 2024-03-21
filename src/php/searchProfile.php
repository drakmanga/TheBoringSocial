<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\SearchProfileService;
use vagrant\TheBoringSocial\php\class\UserService;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/searchProfile.html");


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

    $userService = new UserService($servername,$username,$password);


    $searchProfileService = new SearchProfileService($servername,$username,$password);

    $user = $userService->catchUserData($_SESSION["user"]);
    

    $logger = new Logger('Search Profile Logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " ". $user->getSurname(), $html);

    if (isset($_POST["searchSubmit"])) {
        if (empty($_POST["search"])) {
            header("Location: searchProfile.php");
            die();
        }else {
            header(sprintf("Location: searchProfile.php?username=%s",$_POST["search"]));
            die;
        }
    }

    $profileData = $searchProfileService->catchUsers();
   

    if (!empty($_GET)) {
        $profileData = $searchProfileService->catchUsersFromParameters($_GET["username"]);
    }

    $profiles="";

    foreach ($profileData as $profile) {
        $profiles = $profiles . sprintf('
        
        <div class="col mb-3">
            <div class="card">
                <img src="../profileBackground.jpg" alt="Cover" class="card-img-top">
                <div class="card-body text-center">
                    <a href="profile.php?username=%s"> 
                        <img src="%s" style="margin-top:-100px" alt="User" class="img-fluid img-thumbnail rounded-circle border-0 myclassdrak">
                    </a>
                    <h5 class="card-title">%s %s</h5>
                    <p class="text-secondary mb-1">%s</p>
                    <p class="text-muted font-size-sm">%s</p>
                </div>
                <div class="card-footer">
                    
                        <a class="btn btn-light btn-sm" href="profile.php?username=%s" role="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                                <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z"/>
                            </svg>
                            ViewProfile
                        </a>
                    
                </div>
            </div>
        </div>',  $profile->getUsername(),$profile->getImagePath(), $profile->getName(), $profile->getSurname(), $profile->getGender(), $profile->getCity(), $profile->getUsername());
    }
    
    $html = str_replace("<!-- profile -->", $profiles, $html);

    echo $html;


} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}
