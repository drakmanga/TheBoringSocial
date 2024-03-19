<?php
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\UserService;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/dashboard.html");

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

    $logger = new Logger('Dashboard');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $user = $userService->catchUserData($_SESSION["user"]);
    $logger->info(sprintf('Utente %s si trova nella dashboard', $user->getUsername()));

    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " ". $user->getSurname(), $html);
    echo $html;

} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}
