<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use vagrant\TheBoringSocial\php\class\Logout;
use vagrant\TheBoringSocial\php\class\FileService;
use vagrant\TheBoringSocial\php\class\PostService;
use vagrant\TheBoringSocial\php\class\UserService;
use vagrant\TheBoringSocial\php\class\CommentService;
use vagrant\TheBoringSocial\php\class\SearchProfileService;
require "../../vendor/autoload.php";

$html = file_get_contents("../html/profile.html");


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

    $userService = new UserService($servername,$username,$password);
    $searchProfileService = new SearchProfileService($servername,$username,$password);
    $postService = new PostService($servername,$username,$password);
    $fileService = new FileService($servername,$username,$password);
    $commentService = new CommentService($servername,$username,$password);

    $user = $userService->catchUserData($_SESSION["user"]);
    $userProfile = $searchProfileService->catchUserFromParameters($_GET["username"]);

    $html = str_replace("%imageProfile%", $user->getImagePath(), $html);
    $html = str_replace("%username%", $user->getUsername(), $html);
    $html = str_replace("%nameAndSurname%", $user->getName() . " ". $user->getSurname(), $html);

    $html = str_replace("%imageProfileUser%", $userProfile->getImagePath(), $html);
    $html = str_replace("<!-- description  -->", $userProfile->getDescription(), $html);
    $html = str_replace("<!-- joined -->", $userProfile->getCreation_date() , $html);
    $html = str_replace("<!-- city -->", $userProfile->getCity() , $html);
    $html = str_replace("<!-- email -->", $userProfile->getEmail() , $html);
    $html = str_replace("<!-- website -->", $userProfile->getWebPage() , $html);
    $html = str_replace("<!-- usernameUser -->", $userProfile->getUsername() , $html);
    $html = str_replace("<!-- nameAndSurnameUser -->", $userProfile->getName() . " " . $userProfile->getSurname() , $html);
    
    
    

    $logger = new Logger('Search Profile Logger');
    $logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $comment="";   
    $filePost="";    
    $file = "";   
    $newPost="";


    $postUser = $postService->getMyPubblicatedPost($userProfile->getId());

    foreach ($postUser as $post) {

        if ($fileService->checkIfPostHaveFileOrNot($post->getId())) {

            $filePost = $fileService->catchFilePostFromId($post->getId());

            if ($filePost->getTypology() == "image") {
                 
                $file = sprintf('<div class="card" style="width: 18rem;">
                                    <img src="%s">
                                 </div>',$filePost->getPath());

            }elseif ($filePost->getTypology() == "video") {
                
                $file = sprintf('<div class="card" style="width: 18rem;">
                                    <video src="%s" controls></video>
                                 </div>',$filePost->getPath());
            }else{
                $file = "<!-- FILE -->";
            }
        }
        
       
        
        
        $commentsPost = $commentService->getCommentPost($post->getId(), 3);
       
        foreach($commentsPost as $commentPost) {

            $userData = $userService->catchUserDataWithId($commentPost->getUser_Id());
            
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

        $allCommentPost = $commentService->getCommentPost($post->getId());

        ($post->getUpdatedPost()) ? $update = "Updated At" : $update = "Publicated At";

        (!empty($post->getUpdatedPost())) ? $datePublicateOrUpdate = $post->getDateUpdate() : $datePublicateOrUpdate = $post->getDate();
        
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
                <span class="userimage"><img src="%s" alt=""></span>
                <span class="username"><a href="javascript:;">%s</a> <small></small></span>
                <span style="float:right;"> %s at %s </span>
            </div>
            <div class="timeline-content">
                <p> <h5>%s</h5> </p>
                <p> 
                    %s
                </p>
                
            </div>
            <div class="timeline-likes">
                <div class="stats-right">
                <i class="fa fa-comments fa-fw fa-lg m-r-3"></i><a href="../php/commentPage.php?post_id=%s" type="url" class="m-r-15 text-inverse-lighter" name="moreComment%s"><span class="stats-text">%s Comments</span>
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
                
                    <i class="fa fa-comments fa-fw fa-lg m-r-3"></i> <a href="../php/commentPage.php?post_id=%s" type="url" class="m-r-15 text-inverse-lighter" name="moreComment%s"> See more Comments </a>
                
                
            </div>
            <div class="timeline-comment-box">
                <div class="user"><img src="%s"></div>
                <div class="input">
                    <form method="post" action="../php/myPost.php">
                        <div class="input-group">

                            <input type="hidden" name="postId" value="%s" />
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
        <li>',  
                $post->getDate(), $userProfile->getImagePath(), $userProfile->getName() . " ". $userProfile->getSurname(),
                $update, $datePublicateOrUpdate, $post->getDescription(), $file ,$post->getId(),$post->getId(), count($allCommentPost),
                $post->getId(), $post->getId(), $user->getImagePath(), $post->getId(), $comment);        
    }
    $html = str_replace("<!-- newPost -->", $newPost, $html);

    echo $html;


} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}