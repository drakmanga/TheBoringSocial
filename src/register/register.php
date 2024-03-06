<?php 
use vagrant\TheBoringSocial\php\class\Database;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\User;

require "../../vendor/autoload.php";

$html = file_get_contents("register.html");
echo $html;

$servername = "localhost";
$username = "root";
$password = "exercise";

date_default_timezone_set('Europe/Rome');

try {
    
    $database = new Database ($servername,$username,$password);
    
    die;

    if (!empty($_POST["username"]) && ($_POST["pswd"]) && ($_POST["email"]) && ($_POST["birthday"])) {

        $usernameCheck = $database->checkUsernameAndPrintError($_POST["username"]);
        
        $passwordCheck = Password::checkAndPrintErrorPassword($_POST["pswd"]);
        $cryptPswd = Password::cryptPswd($passwordCheck);
        
        $emailCheck = $database->checkEmailAndPrintError($_POST["email"]);

        $dateTime= date("Y-m-d H:i:s");

        !User::validateAge($_POST["birthday"]) ? ($birthday = $_POST["birthdat"]) : ("echo 'devi essere maggiorenne per iscriverti'" . die);
       
        if (array_key_exists("file", $_FILES)) {
            $file = "/home/vagrant/exercise/TheBoringSocial/src/photoUser/". $_FILES['file']['name'];
            $image = "/photoUser/". $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
        };
        
        $database->addNewUser($usernameCheck,$cryptPswd, $emailCheck, $dateTime, $birthday);
        $user = $database->catchUserData($usernameCheck);
        rename($image, "/home/vagrant/exercise/TheBoringSocial/src/photoUser/" . $user->getId() . ".jpeg");

        header("Location: ../index/index.php");
            die();   
    } else {

        echo "<div class='d-flex align-items-center justify-content-center'><p class='text-danger'>Inserire tutti i campi </p> </div> ";
    }
    
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}