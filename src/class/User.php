<?php
namespace vagrant\TheBoringSocial\class;


class User {
    private $id;
    private $username;
    private $password;
    private $email;
    private $birthday;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    public static function validateAge($birthday, $age = 18)
{
    if(is_string($birthday)) {
        $birthday = strtotime($birthday);
    }

    if(time() - $birthday < $age * 31536000)  {
        return false;
    }

}
}