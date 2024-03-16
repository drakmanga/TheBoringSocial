<?php

namespace vagrant\TheBoringSocial\php\class;
use PDO;
use vagrant\TheBoringSocial\php\class\Post;
use vagrant\TheBoringSocial\php\class\User;
use vagrant\TheBoringSocial\php\class\Comment;
use vagrant\TheBoringSocial\php\class\Database;

class DbFunction extends Database{
    
    public function checkUsernameAndPrintError($username) {
        $dataInput = [
            'username' => $username,
        ];
       
        $sql = "SELECT * FROM userData WHERE username = :username ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject();
        
        if (!empty($result)) {
            echo "<div class=container 'mt-3'><p class='text-danger'> username già utilizzato </p> </div>";
            die;
        }else return $username;
    }
        
    public function checkEmailAndPrintError($email) {
        $dataInput = [
            'email' => $email,
        ];
       
        $sql = "SELECT * FROM userData WHERE email = :email ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject();

        if (!empty($result)) {
            echo "<div class=container 'mt-3'><p class='text-danger'> Email già utilizzata </p> </div>";
            die;
        }else return $email; 
    }

    public function catchUserData($username) {
        $dataInput = [
            'username' => $username,
            
        ];
        $sql = "SELECT * FROM userData WHERE username = :username";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $result = $stmt->fetch();
        return $result;
    }

    public function catchUserDataWithId($user_id) {
        $dataInput = [
            'id' => $user_id,
            
        ];
        $sql = "SELECT * FROM userData WHERE id = :id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $result = $stmt->fetch();
        return $result;
    }

    public function addNewUser($username, $password, $email, $dateTime, $birthday, $name, $surname, $city, $gender, $language) {
        $dataInput = [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'creation_date' => $dateTime,
            'birthday' => $birthday,
            'name'=>$name,
            'surname'=>$surname,
            'city'=>$city,
            'gender'=>$gender,
            'language'=>$language
        ];
        $sql = "INSERT INTO userData (username, password, email, creation_date, birthday, name, surname, city, gender, language) 
                VALUES (:username, :password, :email, :creation_date, :birthday, :name, :surname, :city, :gender, :language)";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function addImagePath($username,$imagePath) {
        $dataInput = [
            'username' => $username,
            'imagePath'=> $imagePath
        ];
        $sql = "UPDATE userData 
                SET imagePath = :imagePath
                WHERE username = :username";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function changePasswordFromUserData($password, $username, $name, $surname, $email) {
        $dataInput = [
            'username' => $username,
            'name'=> $name,
            'surname'=>$surname,
            'email'=>$email,
            'password'=>$password
        ];

        $sql = "UPDATE userData
                SET password = :password
                WHERE username = :username
                    AND name = :name
                    AND surname = :surname
                    AND email = :email";

        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
       
    }

    public function checkUserData ($username,$name,$surname,$email) {
        $dataInput = [
            'username' => $username,
            'name'=> $name,
            'surname'=>$surname,
            'email'=>$email
        ];
        $sql = "SELECT * FROM userData
                WHERE username = :username
                        AND name = :name
                        AND surname = :surname
                        AND email = :email";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $result = $stmt->fetch();
        if (!empty($result)) return true;
        return false;
    }

    public function updateNewPassword($username, $password) {
        $dataInput = [
            'password' => $password,
            'username'=>$username
        ];
        $sql = "UPDATE userData
                SET password = :password
                WHERE username = :username";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function updateInfoUser ($username, $colum, $data) {
        $dataInput = [
            'username'=>$username,
            'data'=>$data
        ];

        $sql = sprintf("UPDATE userData
                SET %s = :data
                WHERE username = :username",$colum);
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function changeUsernameAndPrintError($username) {
        $dataInput = [
            'username' => $username,
        ];
       
        $sql = "SELECT * FROM userData WHERE username = :username ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject();
        
        if (empty($result)) return $username;
    }

    public function getMyPubblicatedPost($user_id) {
        $dataInput = [
            'user_id' => $user_id
        ];

        $sql = "SELECT * FROM post WHERE user_id = :user_id 
                    ORDER BY date DESC";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $result = $stmt->fetchAll();
        return $result;
    }
    
    public function getAllLikeFromPost($post_id) {
        $dataInput = [
            'post_id' => $post_id
        ];

        $sql = "SELECT * FROM likedPost WHERE post_id = :post_id ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function addCommentToPost($post_id, $user_id, $comment, $date) {
        $dataInput = [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'comment' => $comment,
            'date' => $date
        ];
        $sql = "INSERT INTO commentPost (post_id, user_id, comment, date) 
                VALUES (:post_id, :user_id, :comment, :date)";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function addNewPost($user_id, $description, $date) {
        $dataInput = [
            'user_id' => $user_id,
            'description' => $description,
            'date' => $date
        ];
        $sql = "INSERT INTO post (user_id, description, date) 
                VALUES (:user_id, :description, :date)";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }
    
    public function getCommentPost ($post_id, $limit = null) {
        $dataInput = [
            'post_id' => $post_id,
            
        ];
            $sql = "SELECT * FROM commentPost  WHERE post_id = :post_id
                    ORDER BY date DESC";
            if ($limit) $sql = "SELECT * FROM commentPost  WHERE post_id = :post_id 
                                ORDER BY date DESC
                                LIMIT 3";

        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Comment::class);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function getPostFromDb($post_id) {
        $dataInput = [
            'post_id' => $post_id
        ];
        $sql = "SELECT * FROM post WHERE id = :post_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $result = $stmt->fetch();
        return $result;
    }

    public function updatePost($post_id, $newPost, $dateUpdate) {
        
        $dataInput = [
            'post_id' => $post_id,
            'description' => $newPost,
            'dateUpdate' => $dateUpdate
        ];
        
        $sql = "UPDATE post
                SET description = :description, 
                    dateUpdate = :dateUpdate,
                    updatedPost = true
                WHERE id = :post_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }
    

    public function removePost($post_id) {

        $dataInput = [
            'post_id' => $post_id,
        ];
        $sql = "DELETE FROM post WHERE id = :post_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        
    }

    public function removeCommentsPost($post_id) {
        $dataInput = [
            'post_id' => $post_id,
        ];
        $sql = "DELETE FROM commentPost WHERE post_id = :post_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        return $this;
    }
}