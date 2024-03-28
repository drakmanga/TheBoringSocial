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
use vagrant\TheBoringSocial\php\class\LikeService;

require "../../vendor/autoload.php";

$html = file_get_contents("../html/commentPage.html");
date_default_timezone_set('Europe/Rome');

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

    $postService = new PostService($servername, $username, $password);
    $commentService = new CommentService($servername, $username, $password);
    $fileService = new FileService($servername, $username, $password);
    $userService = new UserService($servername, $username, $password);
    $likeService = new LikeService($servername, $username, $password);


    $logger = new Logger('CommentPage');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/my_app.log', Level::Debug));
    $logger->pushHandler(new FirePHPHandler());

    $postId = $_GET["post_id"];
    $postForCatchUser = $postService->getPostFromDb($postId);

    $user = $userService->catchUserDataWithId($postForCatchUser->getUser_id());
    $logger->info(sprintf('Utente %s si trova nella pagina del post %s', $user->getUsername(), $_GET["post_id"]));



    $comment = "";
    $file = "";

    if (isset($_POST["updatepost"])) {
        if (!empty($_POST["updatePost"])) {
            $postId = $_POST["postId"];
            $newPost = ucfirst($_POST["updatePost"]);
            $dateTime =  date("Y-m-d H:i:s");
            $postService->updatePost($postId, $newPost, $dateTime);
            $logger->info(sprintf('Utente %s ha modificato il suo post %s', $user->getUsername(), $postId));
        }

        if ($_FILES['file']['error'] != 4) {
            $postId = $_POST["postId"];
            if (array_key_exists("file", $_FILES)) {
                $file = "/home/vagrant/exercise/TheBoringSocial/src/filePost/" . $_FILES['file']['name'];
                move_uploaded_file($_FILES['file']['tmp_name'], $file);
                $nameUnique = rand(1, 200000);
            };

            $extension = explode("/", $_FILES['file']['type']);
            $typology = (explode("/", $_FILES['file']['type']));
            rename($file, "/home/vagrant/exercise/TheBoringSocial/src/filePost/" . $user->getId() . "postNumber" . $postId . $nameUnique . "." . $extension[1]);

            $newPathImage =  sprintf("/TheBoringSocial/src/filePost/%s%s%s%s.%s", $user->getId(), "postNumber", $postId, $nameUnique, $extension[1]);

            if ($fileService->checkIfPostHaveFileOrNot($postId)) {
                $filePost = $fileService->catchFilePostFromId($postId);
                $nameFile = (explode("/", $filePost->getPath()));
                unlink("/home/vagrant/exercise/TheBoringSocial/src/filePost/" . $nameFile[4]);
                $fileService->UpdateFilePath($postId, $newPathImage, $typology[0]);
            } else {
                $fileService->addFilePath($postId, $newPathImage, $typology[0], $user->getId());
            }
        }
    }

    if (isset($_POST["like"])) {
        $dateTime = date("Y-m-d H:i:s");
        $postId = $_POST["postId"];
        $likeService->addLikeToPost($postId, $user->getId(), $dateTime);
        $logger->info(sprintf('Utente %s ha messo mi piace al post di %s', $user->getUsername(), $postId));
    }

    if (isset($_POST["liked"])) {
        $postId = $_POST["postId"];
        $likeService->removeLikeFromPost($postId, $user->getId());
        $logger->info(sprintf('Utente %s ha rimosso il mi piace al post di %s', $user->getUsername(), $postId));
    }

    if (isset($_POST["submitComment"])) {
        $dateTime = date("Y-m-d H:i:s");
        $commentService->addCommentToPost($postId, $user->getId(), $_POST["comment"], $dateTime);
        $logger->info(sprintf('Utente %s ha commentato il post %s', $user->getUsername(), $postId));
    }

    if (isset($_POST["removePost"])) {
        $postId = $_GET["post_id"];
        $file = $fileService->catchFilePostFromId($postId);

        if ($file) $fileService->removeFilePost($postId, explode("/", $file->getPath()));

        $commentService->removeCommentsPost($postId);
        $postService->removePost($postId);

        $logger->info(sprintf('Utente %s ha rimosso  il suo post %s', $user->getUsername(), $postId));
        header("Location: myPost.php");
    }
    $post = $postService->getPostFromDb($postId);
    ($post->getUpdatedPost()) ? $update = "Updated At" : $update = "Publicated At";
    (!empty($post->getUpdatedPost())) ? $datePublicateOrUpdate = $post->getDateUpdate() : $datePublicateOrUpdate = $post->getDate();

    if ($fileService->checkIfPostHaveFileOrNot($post->getId())) {

        $filePost = $fileService->catchFilePostFromId($post->getId());

        if ($filePost->getTypology() == "image") {

            $file = sprintf('<div class="card" style="width: 18rem;">
                                <img src="%s">
                             </div>', $filePost->getPath());
        } elseif ($filePost->getTypology() == "video") {

            $file = sprintf('<div class="card" style="width: 18rem;">
                                <video src="%s" controls></video>
                             </div>', $filePost->getPath());
        } else {
            $file = "<!-- FILE -->";
        }
    }

    if ($likeService->checkIfUserLikePostOrNot($post->getId(), $user->getId())) {

        $like = '<button class="btn btn-sm btn-primary"  name="liked" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                        <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
                    </svg>
                Unlike </button>';
    } elseif (!$likeService->checkIfUserLikePostOrNot($post->getId(), $user->getId())) {

        $like = '<button class="btn btn-sm btn-light"  name="like" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                        <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                    </svg>
                Like</button>';
    } else {
        $like = "<!-- LIKE -->";
    }

    $commentsPost = $commentService->getCommentPost($postId);

    foreach ($commentsPost as $commentPost) {

        $userData = $userService->catchUserDataWithId($commentPost->getUser_Id());

        $comment = $comment . sprintf(
            '
            
        <div class="container">
            <div class="row">
                <div class="col col-lg-1">
                    <a class="pull-left" href="#">
                        <div class="user"><img src="%s" class="myclassdrak"></div>
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
            $userData->getImagePath(),
            $userData->getName() . $userData->getSurname(),
            $commentPost->getDate(),
            $commentPost->getComment()
        );
    }

    $allCommentPost = $commentService->getCommentPost($post->getId());

    (!empty($userData)) ? $userDataImage = $user->getImagePath() : $userDataImage = "";

    $likeCount = $likeService->getAllLikeFromPost($postId);


    $postAndComments = sprintf(
        '
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
                    <form method="post" action="../php/commentPage.php?post_id=%s" enctype="multipart/form-data">
                        <span class="userimage"><img src="%s" alt=""></span>
                        <span class="username"><a href="javascript:;">%s</a> <small></small></span>
                        <span style="float:right;"> %s at %s </span>

                        <!-- bottone rimuovi post -->
                        <div>
                            <span style="position:absolute; top:0; right:0"> 
                                <button type="button" class="btn btn-danger btn-sm " data-bs-toggle="modal" data-bs-target="#exampleModal%s">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                                </button>
                            </span>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal%s" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <button type="button" class="btn btn-primary btn-sm " data-bs-toggle="modal" data-bs-target="#exampleModal%s.1">
                                    Change Post
                                </button>
                            </span>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal%s.1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Change Post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table>
                                            <tr>
                                                <td class="field">Post</td>
                                                <td> <input type="text" class="form-control" name="updatePost" placeholder="%s " maxlength="255"> </td>   
                                                <td> <input type="file" class="form-control" name="file" maxlength="255"> </td>
                                                                                                      
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
                    <i class="fa fa-comments fa-fw fa-lg m-r-3"></i><a href="../php/commentPage.php?post_id=%s" type="url" class="m-r-15 text-inverse-lighter" name="moreComment%s"><span class="stats-text">%s Comments</span>
                        
                    </div>
                    <div class="stats">
                        <span class="fa-stack fa-fw stats-icon">
                        <i class="fa fa-circle fa-stack-2x text-primary"></i>
                        <i class="fa fa-thumbs-up fa-stack-1x fa-inverse"></i>
                        </span>
                        <span class="stats-total">%s</span>
                    </div>
                </div>
                <div class="timeline-footer">
                
                <form method="post" action="../php/commentPage.php?post_id=%s">
                        <input type="hidden" name="postId" value="%s" />

                        %s
                        
                        <i class="fa fa-comments fa-fw fa-lg m-r-3"></i> <a href="../php/commentPage.php?post_id=%s" type="url" class="m-r-15 text-inverse-lighter" name="moreComment%s"> See more Comments </a>
                    </form>
                    
                </div>
                <div class="timeline-comment-box">
                    <div class="user"><img src="%s"></div>
                    <div class="input">
                        <form method="post" action="../php/commentPage.php?post_id=%s">
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
        $post->getDate(),
        $postId,
        $user->getImagePath(),
        $user->getName() . " " . $user->getSurname(),
        $update,
        $datePublicateOrUpdate,
        $postId,
        $postId,
        $postId,
        $postId,
        $postId,
        $post->getDescription(),
        $postId,
        $post->getDescription(),
        $file,
        $postId,
        $postId,
        count($allCommentPost),
        count($likeCount),
        $postId,
        $postId,
        $like,
        $postId,
        $postId,
        $userDataImage,
        $postId,
        $postId,
        $comment
    );

    $html = str_replace("<!-- post -->", $postAndComments, $html);

    echo $html;
} catch (PDOException $e) {
    $logger->error($e->getMessage());
    echo "Connection failed: " . $e->getMessage();
}
