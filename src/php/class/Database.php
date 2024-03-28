<?php

namespace vagrant\TheBoringSocial\php\class;

use PDO;

abstract class Database
{
    protected $pdo;

    public function __construct($servername, $username, $password)
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO("mysql:host=$servername;dbname=TheBoringSocial", $username, $password, $options);
    }
}
