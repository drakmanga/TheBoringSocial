<?php
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\DbFunction;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/editProfile.html");


session_start();

if (empty($_SESSION["user"])) {
    header("Location: login.php");
    die();
}
if (isset($_POST["logout"])) {
    $_SESSION = array();
    Logout::logout();
    
}

$servername = "localhost";
$username = "root";
$password = "exercise";

try {



    $dbFunction = new DbFunction($servername,$username,$password);
    $user = $dbFunction->catchUserData($_SESSION["user"]);

    $logger = new Logger('Edit Profile logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    if (isset($_POST["update"])) {

        if (!empty($_POST["name"])) {
            $name = ucfirst($_POST["name"]);
            $dbFunction->updateInfoUser($user->getUsername(), "name", $name);
            $logger->info(sprintf('Utente %s ha modificato il suo nome', $user->getUsername()));
        }
        if (!empty($_POST["surname"])) {
            $surname = ucfirst($_POST["surname"]);
            $dbFunction->updateInfoUser($user->getUsername(), "surname", $surname);
            $logger->info(sprintf('Utente %s ha modificato il suo cognome', $user->getUsername()));
        }
        if (!empty($_POST["description"])) {
            $description = $_POST["description"];
            $dbFunction->updateInfoUser($user->getUsername(), "description", $description);
            $logger->info(sprintf('Utente %s ha modificato la sua descrizione', $user->getUsername()));
        }
        if (!empty($_POST["city"])) {
            $city = ucfirst($_POST["city"]);
            $dbFunction->updateInfoUser($user->getUsername(), "city", $city);
            $logger->info(sprintf('Utente %s ha modificato la sua città', $user->getUsername()));
        }
        if (!empty($_POST["gender"])) {
            $gender = ucfirst($_POST["gender"]);
            $dbFunction->updateInfoUser($user->getUsername(), "gender", $gender);
            $logger->info(sprintf('Utente %s ha modificato il suo gender', $user->getUsername()));
        }
        if (!empty($_POST["language"])) {
            $language = ucfirst($_POST["language"]);
            $dbFunction->updateInfoUser($user->getUsername(), "language", $language);
            $logger->info(sprintf('Utente %s ha modificato la sua lingua', $user->getUsername()));
        }
        if (!empty($_POST["webpage"])) {
            $webPage = "https://" . $_POST["webPage"];
            $dbFunction->updateInfoUser($user->getUsername(), "webPage", $webPage);
            $logger->info(sprintf('Utente %s ha modificato la sua webPage', $user->getUsername()));
        }

        if ($_FILES['file']["error"] != 4) {
            if (array_key_exists("file", $_FILES)) {

                $nameImage = explode("/",$user->getImagePath());
                if (file_exists("/home/vagrant/exercise/TheBoringSocial/src/photoUser/" . $nameImage[4])); {
                    unlink("/home/vagrant/exercise/TheBoringSocial/src/photoUser/" . $nameImage[4]);
                }
                $file = "/home/vagrant/exercise/TheBoringSocial/src/photoUser/". $_FILES['file']['name'];
                move_uploaded_file($_FILES['file']['tmp_name'], $file);

                $extension= explode("/",$_FILES['file']['type']);
                rename($file, "/home/vagrant/exercise/TheBoringSocial/src/photoUser/" . $user->getId() . "." . $extension[1]);
                $newPathImage =  sprintf("/TheBoringSocial/src/photoUser/%s.%s", $user->getId(), $extension[1]);
                $dbFunction->addImagePath($user->getUsername(), $newPathImage);
                $logger->info(sprintf('Utente %s ha modificato la sua immagine profilo', $user->getUsername()));
            };
        }
    }

    $user = $dbFunction->catchUserData($_SESSION["user"]);
    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " ". $user->getSurname(), $html);
    $html = str_replace("%description%", $user->getDescription(), $html);
    $html = str_replace("%city%", $user->getCity(), $html);
    $html = str_replace("%gender%", $user->getGender(), $html);
    $html = str_replace("%birthday%", $user->getBirthday(), $html);
    $html = str_replace("%language%", $user->getLanguage(), $html);
    $html = str_replace("%name%", $user->getName(), $html);
    $html = str_replace("%surname%", $user->getSurname(), $html);
    $html = str_replace("%webpage%", $user->getWebPage(), $html);
    echo $html;

} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}
