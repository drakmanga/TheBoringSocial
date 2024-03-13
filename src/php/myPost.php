<?php   

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\DbFunction;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/myPost.html");

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
    
    $post="";

    $logger = new Logger('My post');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $user = $dbFunction->catchUserData($_SESSION["user"]);
    $logger->info(sprintf('Utente %s si trova nella sezione MyPost', $user->getUsername()));

    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " ". $user->getSurname(), $html);

        // if (isset($_POST["newPost"])) {
        //     if (!empty($_POST["post"])) {
        //         $dateTime= date("Y-m-d H:i:s");
        //         $dbFunction->addNewPost($user->getId(), $_POST["post"], $dateTime);

        //         if (array_key_exists("file", $_FILES)) {
        //             $file = "/home/vagrant/exercise/TheBoringSocial/src/filePost/". $_FILES['file']['name'];
        //             move_uploaded_file($_FILES['file']['tmp_name'], $file);
        //         };
        
        //         $extension= explode("/",$_FILES['file']['type']);
        //         rename($file, "/home/vagrant/exercise/TheBoringSocial/src/filePost/" . $user->getId() . "postNumber" . $post"." . $extension[1]);
        //         $newPathImage =  sprintf("/TheBoringSocial/src/filePost/%s.%s", $user->getId(), $extension[1]);
        //         $dbFunction->addImagePath($user->getUsername(), $newPathImage);
        //     }
        // }

       
        $allPost= $dbFunction->getMyPubblicatedPost($user->getId());
        $newPost="";
        $comment="";
        $string="";

        
        
        foreach (array_reverse($allPost) as $post) {


            $modifyPost = '
            <div class="panel rounded-3">
                        <div class="panel-body">
                            <form method="post" action="../php/myPost.php">
                                <input type="text" maxlength="255" name="post" placeholder="new content">
                                <div class="mar-top clearfix">
                                    <button class="btn btn-sm btn-primary pull-right"  name="newPost" type="submit"><i class="fa fa-pencil fa-fw"></i> Modify</button>
                                </div>
                            </form>
                        </div>
                    </div>
            ';
            
           $likeCount = count($dbFunction->getAllLikeFromPost($post->getId()));
            
            if (isset($_POST["submitComment" . $post->getId()])) {
                $dateTime= date("Y-m-d H:i:s");
                $dbFunction->addCommentToPost($post->getId(), $user->getId(), $_POST["comment" . $post->getId()], $dateTime);
                $logger->info(sprintf('Utente %s ha commentato il post %s', $user->getUsername(), $post->getId()));
            }
           
                
           
 
            $commentsPost = $dbFunction->getCommentPost($post->getId(), 3);

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

            $allCommentPost = $dbFunction->getCommentPost($post->getId());
            $newPost = $newPost. sprintf('
           
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
                    
                        <i class="fa fa-comments fa-fw fa-lg m-r-3"></i> <a href="../php/commentPage.php?post_id=%s" type="button" class="btn btn-secondary btn-sm rounded-corner" name="moreComment%s"> See more Comments </a>
                    
                    
                </div>
                <div class="timeline-comment-box">
                    <div class="user"><img src="%s"></div>
                    <div class="input">
                        <form method="post" action="../php/myPost.php">
                            <div class="input-group">
                                <input type="text" class="form-control rounded-corner" name="comment%s" placeholder="Write a comment...">
                                <span class="input-group-btn p-l-10">
                                <button class="btn btn-primary f-s-12 rounded-corner" name="submitComment%s" type="submit">Comment</button>
                                </span>
                            </div>
                        </form>
                            %s
                    </div>
                </div>
            </div>
            <!-- end timeline-body -->
            </li>
            <li>',$post->getDate(),$user->getImagePath(), $user->getName() . " ". $user->getSurname(),
                    $post->getId(), $post->getDescription(), count($allCommentPost),$post->getId(), $post->getId(),
                    $user->getImagePath(), $post->getId(), $post->getId(), $comment);

                     $comment="";             
        }

        if (isset($_POST["modifyPost" . $post->getId()])) {
            
            
            // $logger->info(sprintf('Utente %s ha aggiornato il post %s', $user->getUsername(), $post->getId()));
            
        }
            $html = str_replace("<!-- newPost -->", $newPost , $html);

            
    echo $html;


} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}