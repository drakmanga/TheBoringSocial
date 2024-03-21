<?php
namespace vagrant\TheBoringSocial\php\class;


class User {
    private $id;
    private $username;
    private $password;
    private $email;
    private $birthday;
    private $name;
    private $surname;
    private $imagePath;
    private $city;
    private $description;
    private $gender;
    private $language;
    private $webPage;
    private $creation_date;

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
 
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    public function getImagePath()
    {
        return $this->imagePath;
    }

    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }
 
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }
 
    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    public function getWebPage()
    {
        return $this->webPage;
    }

    public function setWebPage($webPage)
    {
        $this->webPage = $webPage;

        return $this;
    }

    public function getCreation_date()
    {
        return $this->creation_date;
    }

    public function setCreation_date($creation_date)
    {
        $this->creation_date = $creation_date;

        return $this;
    }
}