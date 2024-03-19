<?php

namespace vagrant\TheBoringSocial\php\class;



use vagrant\TheBoringSocial\php\class\Database;

class LikeService extends Database {
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
}