<?php
use vagrant\TheBoringSocial\php\class\DbFunction;
use vagrant\TheBoringSocial\php\class\Password;

require "../../vendor/autoload.php";


$html = file_get_contents("../html/newPwd.html");
echo $html;

session_start();

$servername = "localhost";
$username = "root";
$password = "exercise";

try {

    $dbFunction = new DbFunction($servername,$username,$password);

    if (isset($_POST['name']) && ($_POST['username']) && ($_POST['surname']) && ($_POST['email'])) {
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $userUsername = $_POST["username"];
        $temporaryPassword = Password::generateNewTemporaryPassword();
        
        if ($dbFunction->checkUserData($userUsername,$name,$surname,$email)) {
            $_SESSION["user"] = $userUsername;
            $dbFunction->changePasswordFromUserData($temporaryPassword, $userUsername, $name, $surname, $email);
            header("Location: creationNewPwd.php");
            die();
        }
       
        
    }
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}