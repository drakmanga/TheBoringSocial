<?php 
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\DbFunction;
use vagrant\TheBoringSocial\php\class\UserValidation;


require "../../vendor/autoload.php";

$html = file_get_contents("../html/register.html");

$servername = "localhost";
$username = "root";
$password = "exercise";

date_default_timezone_set('Europe/Rome');

try {
    
    $dbFunction = new DbFunction($servername,$username,$password);

    $logger = new Logger('Register Logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    if (isset($_POST["register"])) {
        if (!empty($_POST["username"]) && ($_POST["pswd"]) && ($_POST["email"]) && ($_POST["birthday"]) && ($_POST["name"]) 
                    && ($_POST["surname"]) && ($_POST["city"]) && ($_POST["gender"])  && ($_POST["language"])) {

            $name = ucfirst($_POST["name"]);
            $surname = ucfirst($_POST["surname"]);
            $city = ucfirst($_POST["city"]);
            $gender = ucfirst($_POST["gender"]);
            $language = ucfirst($_POST["language"]);

            
            $usernameCheck = $dbFunction->checkUsernameAndPrintError($_POST["username"]);
            
            $passwordCheck = Password::checkAndPrintErrorPassword($_POST["pswd"]);
            $cryptPswd = Password::cryptPswd($passwordCheck);
            
            $emailCheck = $dbFunction->checkEmailAndPrintError($_POST["email"]);

            $dateTime= date("Y-m-d H:i:s");

            !UserValidation::validateAge($_POST["birthday"]) ? ($birthday = $_POST["birthday"]) : ("echo 'devi essere maggiorenne per iscriverti'" . die);
        
            $dbFunction->addNewUser($usernameCheck,$cryptPswd, $emailCheck, $dateTime, $birthday, $name, $surname, $city, $gender, $language);
            $user = $dbFunction->catchUserData($usernameCheck);

        
            if (array_key_exists("file", $_FILES)) {
                $file = "/home/vagrant/exercise/TheBoringSocial/src/photoUser/". $_FILES["file"]["name"];
                move_uploaded_file($_FILES["file"]["tmp_name"], $file);
                $extension= explode("/",$_FILES["file"]["type"]);
                rename($file, "/home/vagrant/exercise/TheBoringSocial/src/photoUser/" . $user->getId() . "." . $extension[1]);
                $newPathImage =  sprintf("/TheBoringSocial/src/photoUser/%s.%s", $user->getId(), $extension[1]);
                $dbFunction->addImagePath($user->getUsername(), $newPathImage);
            }
            

            $logger->info('Un nuovo utente si è registrato!', ['username' => $usernameCheck]);
            
            header("Location: ../php/login.php");
                die;  

        } else {
            echo "<div class='d-flex align-items-center justify-content-center'><p class='text-danger'>Inserire tutti i campi </p> </div>";
        }
    }
    echo $html;
} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}