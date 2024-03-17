<?php
namespace vagrant\TheBoringSocial\php\class;


class File {
    private $id;
    private $post_id;
    private $path;
    private $typology;
    

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

    public function getPath()
    {
        return $this->path;
    }
 
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
 
    public function getTypology()
    {
        return $this->typology;
    }

    public function setTypology($typology)
    {
        $this->typology = $typology;

        return $this;
    }
}