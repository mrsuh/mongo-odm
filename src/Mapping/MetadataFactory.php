<?php

namespace ODM\Mapping;

class MetadataFactory
{
    /**
     * @var Metadata[]
     */
    private $metadata;

    /**
     * @var MetadataFactory
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
        $this->metadata = [];
    }

    /**
     * @return MetadataFactory
     */
    public static function getInstance()
    {
        if(null === self::$instance) {
            self::$instance = new MetadataFactory();
        }

        return self::$instance;
    }

    /**
     * @param string $class_name
     * @return Metadata
     */
    public function get(string $class_name)
    {
        if(!array_key_exists($class_name, $this->metadata)) {
            $this->metadata[$class_name] = new Metadata($class_name);
        }

        return $this->metadata[$class_name];
    }
}