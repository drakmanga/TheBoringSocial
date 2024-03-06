<?php 

use vagrant\TheBoringSocial\php\class\Database;
use vagrant\TheBoringSocial\php\class\Password;

require "../../vendor/autoload.php";

$html = file_get_contents("index.html");
echo $html;

session_start();

$servername = "localhost";
$username = "root";
$password = "exercise";

try {

	$database = new Database($servername,$username,$password);

	if (isset($_POST["login"])) {
        $user = $database->catchUserData($_POST["username"]);

        if (Password::matchPswd($_POST["username"],$user->getPassword())) {

            $_SESSION["user"] = $user;
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