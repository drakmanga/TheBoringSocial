<?php

namespace vagrant\TheBoringSocial\php\class;


class Like
{
    private $id;
    private $post_id;
    private $user_id;
    private $date;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getPost_id()
    {
        return $this->post_id;
    }

    public function setPost_id($post_id)
    {
        $this->post_id = $post_id;

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

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}
