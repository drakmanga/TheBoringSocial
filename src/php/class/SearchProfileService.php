<?php

namespace vagrant\TheBoringSocial\php\class;

use PDO;
use vagrant\TheBoringSocial\php\class\User;
use vagrant\TheBoringSocial\php\class\Database;

class SearchProfileService extends Database{

    public function catchUsers($user) {
        $dataInput = [
            'user' => $user
        ];

        $sql = "SELECT * FROM userData
                WHERE username != :user";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
        return $result;
    }

    public function catchUsersFromParameters($parameters, $user) {
        $dataInput = [
            'parameters' => $parameters,
            'user' => $user
        ];

        $sql = "SELECT * FROM userData 
                WHERE name LIKE  Concat('%' , :parameters , '%')
                AND username != :user";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
        return $result;
        
    }

    public function catchUserFromParameters($username) {
        $dataInput = [
            'username' => $username
        ];

        $sql = "SELECT * FROM userData WHERE username = :username";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject(User::class);
        return $result;
        
    }
}