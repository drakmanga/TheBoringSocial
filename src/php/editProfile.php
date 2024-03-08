<?php
use vagrant\TheBoringSocial\php\class\DbFunction;
use vagrant\TheBoringSocial\php\class\Logout;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/editProfile.html");


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

    $dbFunction = new DbFunction($servername,$username,$password);

    $user = $dbFunction->catchUserData($_SESSION["user"]);
    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " ". $user->getSurname(), $html);
    $html = str_replace("%description%", $user->getDescription(), $html);
    $html = str_replace("%city%", $user->getCity(), $html);
    $html = str_replace("%gender%", $user->getGender(), $html);
    $html = str_replace("%birthday%", $user->getBirthday(), $html);
    $html = str_replace("%language%", $user->getLanguage(), $html);
    $html = str_replace("%name%", $user->getName(), $html);
    $html = str_replace("%surname%", $user->getSurname(), $html);
    $html = str_replace("%webpage%", $user->getWebPage(), $html);

    if (!empty($_POST["name"])) $name = ucfirst($_POST["name"]);
    if (!empty($_POST["surname"])) $surname = ucfirst($_POST["surname"]);
    if (!empty($_POST["description"])) $description = $_POST["description"];
    if (!empty($_POST["city"])) $city = ucfirst($_POST["city"]);
    if (!empty($_POST["gender"])) $gender = ucfirst($_POST["gender"]);
    if (!empty($_POST["language"])) $language = ucfirst($_POST["language"]);
    if (!empty($_POST["webpage"])) $webPage = "https://" . $_POST["webPage"];
    

    


    
        
        
        
        
    echo $html;

} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
