<?php
use vagrant\TheBoringSocial\php\class\DbFunction;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/editProfile.html");


session_start();

if (empty($_SESSION["user"])) {
    header("Location: login.php");
    die();
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
    echo $html;

} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
