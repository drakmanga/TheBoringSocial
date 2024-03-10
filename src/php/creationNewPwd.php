<?php
use vagrant\TheBoringSocial\php\class\DbFunction;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\Logout;

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
    $dbFunction = new DbFunction($servername,$username,$password);

    if (!empty($_POST["pwd"])) {
        $passwordCheck = Password::checkAndPrintErrorPassword($_POST["pwd"]);
        $cryptPswd = Password::cryptPswd($passwordCheck);
        $dbFunction->updateNewPassword($_SESSION["user"],$cryptPswd);
        $_SESSION = array();
        Logout::logout();
    }
    
    
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
