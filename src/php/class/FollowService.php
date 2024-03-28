<?php

namespace vagrant\TheBoringSocial\php\class;


use PDO;
use vagrant\TheBoringSocial\php\class\Like;
use vagrant\TheBoringSocial\php\class\Follow;
use vagrant\TheBoringSocial\php\class\Database;

class FollowService extends Database
{

    public function checkIfUserFollowOrNot($user_id, $followUser_id)
    {
        $dataInput = [
            'user_id' => $user_id,
            'follow_user_id' => $followUser_id
        ];

        $sql = "SELECT * FROM following 
                WHERE user_id = :user_id
                AND follow_user_id = :follow_user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject(Follow::class);;

        if (!empty($result)) return true;
        return false;
    }

    public function addFollowToUser($user_id, $followUser_id, $date)
    {
        $dataInput = [
            'user_id' => $user_id,
            'follow_user_id' => $followUser_id,
            'date' => $date
        ];
        $sql = "INSERT INTO following (user_id, follow_user_id, date) 
                VALUES (:user_id, :follow_user_id, :date)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function removeFollowToUser($user_id, $followUser_id)
    {
        $dataInput = [
            'user_id' => $user_id,
            'follow_user_id' => $followUser_id
        ];
        $sql = "DELETE FROM following 
                WHERE user_id = :user_id
                AND follow_user_id = :follow_user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function getAllFollowers($followUser_id)
    {
        $dataInput = [
            'follow_user_id' => $followUser_id
        ];

        $sql = "SELECT * FROM following WHERE follow_user_id = :follow_user_id ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Follow::class);
        return $result;
    }

    public function getAllMyFollow($user_id)
    {
        $dataInput = [
            'user_id' => $user_id,

        ];
        $sql = "SELECT * FROM following 
                WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Follow::class);
        return $result;
    }
}
