<?php

namespace vagrant\TheBoringSocial\php\class;

use PDO;

use vagrant\TheBoringSocial\php\class\File;
use vagrant\TheBoringSocial\php\class\Database;

class FileService extends Database
{

    public function removeFilePost($post_id, $pathImage)
    {
        $dataInput = [
            'post_id' => $post_id,
        ];
        $sql = "DELETE FROM file WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);

        unlink("/home/vagrant/exercise/TheBoringSocial/src/filePost/" . $pathImage[4]);

        return $this;
    }

    public function addFilePath($post_id, $path, $typology, $user_id)
    {
        $dataInput = [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'path' => $path,
            'typology' => $typology
        ];

        $sql = "INSERT INTO file (post_id, user_id, path, typology) 
                VALUES (:post_id, :user_id, :path, :typology)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }

    public function UpdateFilePath($post_id, $path, $typology)
    {
        $dataInput = [
            'post_id' => $post_id,
            'path' => $path,
            'typology' => $typology
        ];

        $sql = "UPDATE file
                SET path = :path, 
                    typology = :typology
                WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
    }


    public function catchFilePostFromId($post_id)
    {
        $dataInput = [
            'post_id' => $post_id
        ];

        $sql = "SELECT * FROM file WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject(File::class);;
        return $result;
    }

    public function catchAllPhotoFromId($user_id)
    {
        $dataInput = [
            'user_id' => $user_id,
            'image' => "image"
        ];

        $sql = "SELECT * FROM file 
                WHERE user_id = :user_id
                AND typology = :image";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, File::class);
        return $result;
    }



    public function checkIfPostHaveFileOrNot($post_id)
    {
        $dataInput = [
            'post_id' => $post_id
        ];

        $sql = "SELECT * FROM file WHERE post_id = :post_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dataInput);
        $result = $stmt->fetchObject(File::class);;

        if (!empty($result)) return true;
        return false;
    }
}
