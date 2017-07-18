<?php

namespace ODM\Mapping;

class DataMapperFactory
{
    /**
     * @var DataMapper[]
     */
    private $mappers;

    /**
     * @var DataMapperFactory
     */
    private static $instance;


    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    private function __construct()
    {
        $this->mappers = [];
    }

    /**
     * @return DataMapperFactory
     */
    public static function getInstance()
    {
        if(null === self::$instance) {
            self::$instance = new DataMapperFactory();
        }

        return self::$instance;
    }

    /**
     * @param string $class_name
     * @return DataMapper
     */
    public function get(string $class_name)
    {
        if(!array_key_exists($class_name, $this->mappers)) {
            $this->mappers[$class_name] = new DataMapper($class_name);
        }

        return $this->mappers[$class_name];
    }
}