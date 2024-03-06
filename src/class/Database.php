<?php

namespace vagrant\TheBoringSocial\class;

use PDO;

    class Database {
        private $pdo;

        public function __construct($servername,$username,$password) {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO("mysql:host=$servername;dbname=TheBoringSocial", $username, $password, $options);
        }

        public function checkUsernameAndPrintError($username) {
            $datainput = [
                'username' => $username,
            ];
           
            $sql = "SELECT * FROM userData WHERE username = :username ";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute($datainput);
            $result = $stmt->fetchObject();
            
            if (!empty($result)) {
                echo "<div class=container 'mt-3'><p class='text-danger'> username già utilizzato </p> </div>";
                die;
            }else return $username;
        }
            
        public function checkEmailAndPrintError($email) {
            $datainput = [
                'email' => $email,
            ];
           
            $sql = "SELECT * FROM userData WHERE email = :email ";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute($datainput);
            $result = $stmt->fetchObject();
    
            if (!empty($result)) {
                echo "<div class=container 'mt-3'><p class='text-danger'> Email già utilizzata </p> </div>";
                die;
            }else return $email; 
        }

        public function catchUserData($username) {
            $datainput = [
                'username' => $username,
                
            ];
            $sql = "SELECT * FROM userData WHERE username = :username";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute($datainput);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
            $result = $stmt->fetch();
            return $result;
        }

        public function addNewUser($username, $password, $email, $dateTime, $birthday) {
            $dataInput = [
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'creation_date' => $dateTime,
                'birthday' => $birthday,
            ];
            $sql = "INSERT INTO userData (username, password, email, creation_date, birthday) VALUES (:username, :password, :email, :creation_date, :birthday)";
            $stmt= $this->pdo->prepare($sql);
            $stmt->execute($dataInput);
        }

    }