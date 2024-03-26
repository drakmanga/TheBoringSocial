<?php

namespace vagrant\TheBoringSocial\php\class;

use PDO;
use vagrant\TheBoringSocial\php\class\Like;
use vagrant\TheBoringSocial\php\class\Database;

class LikeService extends Database {

    public function getAllLikeFromPost($post_id) {
        $dataInput = [
            'post_id' => $post_id
        ];

        $sql = "SELECT * FROM likedPost WHERE post_id = :post_id ";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Like::class);
        return $result;
    }

    public function checkIfUserLikePostOrNot($post_id, $user_id) {
        $dataInput = [
            'post_id' => $post_id,
            'user_id' => $user_id
        ];

        $sql = "SELECT * FROM likedPost 
                WHERE post_id = :post_id
                AND user_id = :user_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject(Like::class);;

        if (!empty($result)) return true;
        return false;
    }

    public function addLikeToPost($post_id, $user_id,$date) {
        $dataInput = [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'date' => $date
        ];
        $sql = "INSERT INTO likedPost (post_id, user_id, date) 
                VALUES (:post_id, :user_id, :date)";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function removeLikeFromPost($post_id, $user_id) {
        $dataInput = [
            'post_id' => $post_id,
            'user_id' => $user_id
        ];
        $sql = "DELETE FROM likedPost 
                WHERE post_id = :post_id
                AND user_id = :user_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function removeAllLikeFromPost($post_id) {
        $dataInput = [
            'post_id' => $post_id,
        ];
        $sql = "DELETE FROM likedPost WHERE post_id = :post_id";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        return $this;
    }

}