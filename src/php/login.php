<?php 

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Password;
use vagrant\TheBoringSocial\php\class\DbFunction;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/login.html");
echo $html;

session_start();

$servername = "localhost";
$username = "root";
$password = "exercise";

try {

    $logger = new Logger('Login Logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());


   

    $dbFunction = new DbFunction($servername,$username,$password);

	if (isset($_POST["ogin"])) {
        $user = $dbFunction->catchUserData($_POST["username"]);

        if (Password::matchPswd($_POST["pswd"],$user->getPassword())) {

            $logger->info('Un utente si è loggato correttamente', ['username' => $user->getUsername()]);
            
            $_SESSION["user"] = $user->getUsername();
            header("Location: dashboard.php");
            die();
        }else {
            echo "<div class=container 'mt-3'><p class='text-danger'> username o password errati </p> </div>";
            $logger->error("tentativo di login errato");
            die;
        }
    }

} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}