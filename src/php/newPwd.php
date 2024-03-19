<?php
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\UserService;

require "../../vendor/autoload.php";


$html = file_get_contents("../html/newPwd.html");
echo $html;

session_start();

$servername = "localhost";
$username = "root";
$password = "exercise";

try {

    $userService = new UserService($servername,$username,$password);

    $logger = new Logger('Request New Password');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    if (isset($_POST['name']) && ($_POST['username']) && ($_POST['surname']) && ($_POST['email'])) {
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $userUsername = $_POST["username"];
        $temporaryPassword = Password::generateNewTemporaryPassword();
        
        if ($userService->checkUserData($userUsername,$name,$surname,$email)) {
            $_SESSION["user"] = $userUsername;
            $userService->changePasswordFromUserData($temporaryPassword, $userUsername, $name, $surname, $email);
            $logger->info(sprintf('Utente %s ha richiesto la modifica della sua password', $user->getUsername()));
            header("Location: creationNewPwd.php");
            die();
        }
       
        
    }
} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}