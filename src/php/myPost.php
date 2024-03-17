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

date_default_timezone_set('Europe/Rome');

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

        if (isset($_POST["newPost"])) {
            if (!empty($_POST["post"])) {
                $dateTime= date("Y-m-d H:i:s");
                $dbFunction->addNewPost($user->getId(), ucfirst($_POST["post"]), $dateTime);
                $post_id= $dbFunction->catchLastPost();
            }
            if ($_FILES['file']['error'] != 4) {
                if (array_key_exists("file", $_FILES)) {
                    $file = "/home/vagrant/exercise/TheBoringSocial/src/filePost/". $_FILES['file']['name'];
                    move_uploaded_file($_FILES['file']['tmp_name'], $file);
                   $nameUnique = rand(1, 200000);
                };
                
                $extension= explode("/",$_FILES['file']['type']);
                $typology = (explode("/", $_FILES['file']['type']));
                rename($file, "/home/vagrant/exercise/TheBoringSocial/src/filePost/" . $user->getId() . "postNumber" . $post . $nameUnique . "." . $extension[1]);

                $newPathImage =  sprintf("/TheBoringSocial/src/filePost/%s%s%s%s.%s", $user->getId(), "postNumber", $post, $nameUnique, $extension[1]);
                $dbFunction->addFilePath($post_id->getId(), $newPathImage, $typology[0]);
            }   
            $logger->info(sprintf('Utente %s ha aggiunto un nuovo post. Id: %s', $user->getUsername(), $postId));
        }


        if (isset($_POST["submitComment"])) {
            $dateTime = date("Y-m-d H:i:s");
            $postId = $_POST["postId"];
            $dbFunction->addCommentToPost($postId, $user->getId(), $_POST["comment"], $dateTime);
            $logger->info(sprintf('Utente %s ha commentato il post %s', $user->getUsername(), $postId));
        }

        if (isset($_POST["removePost"])) {
            $postId = $_POST["postId"];
            $file = $dbFunction->catchFilePostFromId($postId);
            
            $dbFunction->removeCommentsPost($postId)->removeFilePost($postId, explode("/",$file->getPath()))->removePost($postId);
            
            $logger->info(sprintf('Utente %s ha rimosso  il suo post %s', $user->getUsername(), $postId));
        }

        if (isset($_POST["updatepost"])) {
            if (!empty($_POST["updatePost"])) { 
                $postId = $_POST["postId"];
                $newPost = ucfirst($_POST["updatePost"]);
                $dateTime =  date("Y-m-d H:i:s");
                $dbFunction->updatePost($postId, $newPost, $dateTime);
                $logger->info(sprintf('Utente %s ha modificato il suo post %s', $user->getUsername(), $postId));
            }
        }     


        $allPost= $dbFunction->getMyPubblicatedPost($user->getId());
        $newPost="";
        $comment="";
        $string="";
        $file="";


        foreach ($allPost as $post) {

            $likeCount = count($dbFunction->getAllLikeFromPost($post->getId()));

            if ($dbFunction->checkIfPostHaveFileOrNot($post->getId())) {

                $filePost = $dbFunction->catchFilePostFromId($post->getId());

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
            
           
            
            
            $commentsPost = $dbFunction->getCommentPost($post->getId(), 3);
           
            foreach($commentsPost as $commentPost) {
    
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
                    <form method="post" action="../php/myPost.php">
                        <span class="userimage"><img src="%s" alt=""></span>
                        <span class="username"><a href="javascript:;">%s</a> <small></small></span>
                        <span style="float:right;"> %s at %s </span>

                        <!-- bottone rimuovi post -->
                        <div>
                            <span style="position:absolute; top:0; right:0"> 
                                <button type="button" class="btn btn-danger btn-sm " data-bs-toggle="modal" data-bs-target="#exampleModal1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                                </button>
                            </span>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel1">Confermi eliminazione post?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-footer">
                                    
                                        <input type="hidden" name="postId" value="%s" />
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="removePost" class="btn btn-primary">Remove</button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- Button trigger modal -->
                        <div>
                            <span style="float:right;"> 
                                <button type="button" class="btn btn-primary btn-sm " data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                    Modifica post
                                </button>
                            </span>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Modifica Post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table>
                                            <tr>
                                                <td class="field">Post</td>
                                                    <td><input type="text" class="form-control" name="updatePost" placeholder="%s " maxlength="255"> </td>   
                                                                                                      
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                    
                                        <input type="hidden" name="postId" value="%s">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="updatepost" class="btn btn-primary">Save changes</button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </form>
                </div>
                <div class="timeline-content">
                    <p> <h5>%s</h5> </p>
                    <p> 
                        %s
                    </p>
                    
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
            <li>',  $post->getDate(), $user->getImagePath(), $user->getName() . " ". $user->getSurname(),
                    $update, $datePublicateOrUpdate, $post->getId(), $post->getDescription(), $post->getId(),
                    $post->getDescription(), $file, count($allCommentPost), $post->getId(), $post->getId(),
                    $user->getImagePath(), $post->getId(), $comment);

                     $comment="";   
                     $filePost="";    
                     $file = "";       
        }

    $html = str_replace("<!-- newPost -->", $newPost , $html);

    
    echo $html;
    


} catch(PDOException $e) {
    $logger->error($e->getMessage());
	echo "Connection failed: " . $e->getMessage();
}