<?php

namespace vagrant\TheBoringSocial\php\class;

use PDO;
use vagrant\TheBoringSocial\php\class\Comment;
use vagrant\TheBoringSocial\php\class\Database;

class CommentService extends Database
{

    public function addCommentToPost($post_id, $user_id, $comment, $date)
    {
        $dataInput = [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'comment' => $comment,
            'date' => $date
        ];
        $sql = "INSERT INTO commentPost (post_id, user_id, comment, date) 
                VALUES (:post_id, :user_id, :comment, :date)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function getCommentPost($post_id, $limit = null)
    {
        $dataInput = [
            'post_id' => $post_id,

        ];
        $sql = "SELECT * FROM commentPost  WHERE post_id = :post_id
                    ORDER BY date DESC";
        if ($limit) $sql = "SELECT * FROM commentPost  WHERE post_id = :post_id 
                                ORDER BY date DESC
                                LIMIT 3";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, Comment::class);
        return $result;
    }

    public function removeCommentsPost($post_id)
    {
        $dataInput = [
            'post_id' => $post_id,
        ];
        $sql = "DELETE FROM commentPost WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        return $this;
    }
}
