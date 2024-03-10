<?php

namespace vagrant\TheBoringSocial\php\class;
use PDO;
use vagrant\TheBoringSocial\php\class\User;
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
}