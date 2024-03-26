<?php
namespace vagrant\TheBoringSocial\php\class;


class Follow {
    private $id;
    private $user_id;
    private $follow_user_id;
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

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getFollow_user_id()
    {
        return $this->follow_user_id;
    }

    public function setFollow_user_id($follow_user_id)
    {
        $this->follow_user_id = $follow_user_id;

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