<?php

namespace ODM\Document;

class Document
{
    /**
     * @ODM\Mapping\Annotator\Field(name="_id", type="string")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}