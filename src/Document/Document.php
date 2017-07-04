<?php

namespace ODM\Document;

class Document
{
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return (string)$this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (string)$id;

        return $this;
    }
}