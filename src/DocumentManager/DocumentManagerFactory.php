<?php

namespace ODM\DocumentMapper;

use ODM\DBAL;

class DocumentManagerFactory
{
    /**
     * @var DBAL
     */
    private $dbal;

    /**
     * @var DocumentManager[]
     */
    private $dms;

    /**
     * DataMapperFactory constructor.
     * @param DBAL $dbal
     */
    public function __construct(DBAL $dbal)
    {
        $this->dbal = $dbal;
        $this->dms = [];
    }

    /**
     * @param string $class_name
     * @return DocumentManager
     */
    public function init(string $class_name)
    {
        if(!array_key_exists($class_name, $this->dms)) {
            $this->dms[$class_name] = new DocumentManager($this->dbal, $class_name);
        }

        return $this->dms[$class_name];
    }
}