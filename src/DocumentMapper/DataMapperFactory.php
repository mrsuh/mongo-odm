<?php

namespace ODM\DocumentMapper;

use ODM\DBAL;

class DataMapperFactory
{
    private $dbal;

    public function __construct(DBAL $dbal)
    {
        $this->dbal = $dbal;
    }

    public function init($class)
    {
        return new DataMapper($this->dbal, $class);
    }
}