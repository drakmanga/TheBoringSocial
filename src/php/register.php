<?php 
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\UserValidation;
use vagrant\TheBoringSocial\php\class\DbFunction;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/register.html");
echo $html;

$servername = "localhost";
$username = "root";
$password = "exercise";

date_default_timezone_set('Europe/Rome');

try {
    
    $dbFunction = new DbFunction($servername,$username,$password);

    $logger = new Logger('register logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    
    if (!empty($_POST["username"]) && ($_POST["pswd"]) && ($_POST["email"]) && ($_POST["birthday"]) && ($_POST["name"]) && ($_POST["surname"])) {

        $name=$_POST["name"];
        $surname=$_POST["surname"];

        $usernameCheck = $dbFunction->checkUsernameAndPrintError($_POST["username"]);
        
        $passwordCheck = Password::checkAndPrintErrorPassword($_POST["pswd"]);
        $cryptPswd = Password::cryptPswd($passwordCheck);
        
        $emailCheck = $dbFunction->checkEmailAndPrintError($_POST["email"]);

        $dateTime= date("Y-m-d H:i:s");

        !UserValidation::validateAge($_POST["birthday"]) ? ($birthday = $_POST["birthday"]) : ("echo 'devi essere maggiorenne per iscriverti'" . die);
       
        $dbFunction->addNewUser($usernameCheck,$cryptPswd, $emailCheck, $dateTime, $birthday, $name, $surname);
        $user = $dbFunction->catchUserData($usernameCheck);

        if (array_key_exists("file", $_FILES)) {
            $file = "/home/vagrant/exercise/TheBoringSocial/src/photoUser/". $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
        };

        $extension= explode("/",$_FILES['file']['type']);
        rename($file, "/home/vagrant/exercise/TheBoringSocial/src/photoUser/" . $user->getId() . "." . $extension[1]);
        $newPathImage =  sprintf("/TheBoringSocial/src/photoUser/%s.%s", $user->getId(), $extension[1]);
        $dbFunction->addImagePath($user->getUsername(), $newPathImage);

        // $logger->info('Adding a new user', ['username' => $usernameCheck]);
        
        header("Location: ../php/login.php");
            die();   
    } else {
        if (isset($_POST["register"])) echo "<div class='d-flex align-items-center justify-content-center'><p class='text-danger'>Inserire tutti i campi </p> </div>";
    }
    
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}