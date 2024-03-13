<?php
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\DbFunction;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/commentPage.html");



session_start();



if (empty($_SESSION["user"])) {
    header("Location: login.php");
    die();
}
if (isset($_POST["logout"])) {
    $_SESSION = array();
    Logout::logout();
}

if (isset($_POST["dashboard"])) {
    header("Location: dashboard.php");
    die();
}



$servername = "localhost";
$username = "root";
$password = "exercise";

try {

    $dbFunction = new DbFunction($servername,$username,$password);

    $logger = new Logger('CommentPage');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $user = $dbFunction->catchUserData($_SESSION["user"]);
    $logger->info(sprintf('Utente %s si trova nella pagina del post %s', $user->getUsername(), $_GET["post_id"]));

    $postId = $_GET["post_id"];
    $post = $dbFunction->getPostFromDb($postId);
    $creatofOfPost  = $dbFunction->catchUserDataWithId($post->getUser_id());

    $comment="";
    


    if (isset($_POST["submitComment"])) {
        $dateTime= date("Y-m-d H:i:s");
        $dbFunction->addCommentToPost($post->getId(), $user->getId(), $_POST["comment"], $dateTime);
        $logger->info(sprintf('Utente %s ha commentato il post %s', $user->getUsername(), $post->getId()));
    }

    $commentsPost = $dbFunction->getCommentPost($postId);

    
    foreach(array_reverse($commentsPost) as $commentPost) {
    
        $userData = $dbFunction->catchUserDataWithId($commentPost->getUser_Id());
        
        $comment = $comment . sprintf('
            
        <div class="container">
            <div class="row">
                <div class="col col-lg-1">
                    <a class="pull-left" href="#">
                        <div class="user"><img src="%s"></div>
                    </a>
                </div>
                <div class="col-sm">
                    <a href="#">
                        <p >%s</hp>
                    </a>
                </div>
                <div class="col-sm">
                    <h8>%s</h5>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-auto">
                    <p>%s</p>
                </div>
                <hr>
            </div>      
        </div>',
        $userData->getImagePath(), $userData->getName() . $userData->getSurname(), $commentPost->getDate(), $commentPost->getComment());
    }

    $postAndComments = sprintf('
    <div class="timeline-time">
        <span class="time">%s</span>
    </div>

    <!-- end timeline-time -->
    <div class="timeline-icon">
        <a href="javascript:;">&nbsp;</a>
    </div>

    <!-- begin timeline-body -->
    <div class="timeline-body">
        <div class="timeline-header">
            <form method="post" action="../php/myPost.php">
                <span class="userimage"><img src="%s" alt=""></span>
                <span class="username"><a href="javascript:;">%s</a> <small></small></span>
                <div class="d-grid d-md-flex justify-content-md-end">
                    <span class="input-group-btn p-l-10">
                        <button class="btn btn-primary btn-sm f-s-12 rounded-corner" name="modifyPost%s" type="submit">Modify Post</button>
                    </span>
                </div>
            </form>
        </div>
        <div class="timeline-content">
            <p> %s </p>
        </div>
        <div class="timeline-likes">
            <div class="stats-right">
                <span class="stats-text">%s Comments</span>
            </div>
            <div class="stats">
                <span class="fa-stack fa-fw stats-icon">
                <i class="fa fa-circle fa-stack-2x text-primary"></i>
                <i class="fa fa-thumbs-up fa-stack-1x fa-inverse"></i>
                </span>
                <span class="stats-total">1</span>
            </div>
        </div>
        <div class="timeline-footer">
            <a href="javascript:;" class="m-r-15 text-inverse-lighter"><i class="fa fa-thumbs-up fa-fw fa-lg m-r-3"></i> Like</a>
 
        </div>
        <div class="timeline-comment-box">
            <div class="user"><img src="%s"></div>
            <div class="input">
                <form method="post" action="../php/commentPage.php?post_id=%s">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-corner" name="comment" placeholder="Write a comment...">
                        <span class="input-group-btn p-l-10">
                        <button class="btn btn-primary f-s-12 rounded-corner" name="submitComment" type="submit">Comment</button>
                        </span>
                    </div>
                </form>
                    %s
            </div>
        </div>
    </div>
    <!-- end timeline-body -->
    </li>
    <li>
    ',$post->getDate(),$user->getImagePath(), $user->getName() . " ". $user->getSurname(),
    $post->getId(), $post->getDescription(), count($commentsPost),
    $user->getImagePath(),$post->getId(), $comment);

    $html = str_replace("<!-- post -->", $postAndComments , $html);
    echo $html;

} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}
