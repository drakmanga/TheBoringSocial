<?php

namespace vagrant\TheBoringSocial\php\class;

class Post {
    private $id;
    private $user_id;
    private $description;
    private $date;
    private $imagePath;
    private $videoPath;
    private $updatedPost;
    private $dateUpdate;


    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

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

    public function getVideoPath()
    {
        return $this->videoPath;
    }
 
    public function setVideoPath($videoPath)
    {
        $this->videoPath = $videoPath;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getUpdatedPost()
    {
        return $this->updatedPost;
    }

    public function setUpdatedPost($updatedPost)
    {
        $this->updatedPost = $updatedPost;

        return $this;
    }
}