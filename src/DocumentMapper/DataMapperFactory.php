<?php

namespace ODM\DocumentMapper;

use ODM\DBAL;

class DataMapperFactory
{
    private $dbal;

    /**
     * DataMapperFactory constructor.
     * @param DBAL $dbal
     */
    public function __construct(DBAL $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * @param $class_name
     * @return DataMapper
     */
    public function init($class_name)
    {
        return new DataMapper($this->dbal, $class_name);
    }
}