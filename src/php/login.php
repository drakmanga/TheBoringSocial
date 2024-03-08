<?php 

use vagrant\TheBoringSocial\php\class\DbFunction;
use vagrant\TheBoringSocial\php\class\Password;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/login.html");
echo $html;

session_start();

$servername = "localhost";
$username = "root";
$password = "exercise";

try {

    $dbFunction = new DbFunction($servername,$username,$password);

	if (isset($_POST["login"])) {
        $user = $dbFunction->catchUserData($_POST["username"]);

        if (Password::matchPswd($_POST["pswd"],$user->getPassword())) {

            $_SESSION["user"] = $user->getUsername();
            header("Location: dashboard.php");
            die();
        }else {
            echo "<div class=container 'mt-3'><p class='text-danger'> username o password errati </p> </div>";
            die;
        }
    }

} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}