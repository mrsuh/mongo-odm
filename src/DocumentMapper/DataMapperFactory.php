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
     * @param $class
     * @return DataMapper
     */
    public function init($class)
    {
        return new DataMapper($this->dbal, $class);
    }
}