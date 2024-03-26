<?php

namespace vagrant\TheBoringSocial\php\class;

use PDO;
use vagrant\TheBoringSocial\php\class\Post;
use vagrant\TheBoringSocial\php\class\Database;

class PostService extends Database {

    public function getMyPubblicatedPost($user_id) {
        $dataInput = [
            'user_id' => $user_id
        ];

        $sql = "SELECT * FROM post WHERE user_id = :user_id 
                    ORDER BY date DESC";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $result;
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

    public function getPostFromDb($post_id) {
        $dataInput = [
            'post_id' => $post_id
        ];
        $sql = "SELECT * FROM post WHERE id = :post_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject(Post::class);
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

    public function catchLastPost() {

        $sql = "SELECT * FROM post 
                ORDER BY date DESC
                LIMIT 1 ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchObject(Post::class);
        return $result;
    }

    public function getAllPost () {
        
        $sql = "SELECT * FROM post ORDER BY date DESC";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $result;
    }

    public function getAllPostFromFollower ($followId) {

        
        $in  = str_repeat('?,', count($followId) - 1) . '?';

        $sql = "SELECT * FROM post 
                WHERE user_id IN ($in) 
                ORDER BY date DESC";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($followId);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $result;

    }


}