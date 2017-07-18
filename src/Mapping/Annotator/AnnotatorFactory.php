<?php

namespace ODM\Mapping\Annotator;

use ODM\Mapping\Metadata;

class AnnotatorFactory
{
    /**
     * @var Annotator[]
     */
    private $annotators;

    /**
     * @var AnnotatorFactory
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
        $this->annotators = [];
    }

    /**
     * @return AnnotatorFactory
     */
    public static function getInstance()
    {
        if(null === self::$instance) {
            self::$instance = new AnnotatorFactory();
        }

        return self::$instance;
    }

    /**
     * @param Metadata $metadata
     * @return Annotator
     */
    public function get(Metadata $metadata)
    {
        $class_name = $metadata->getReflectionClass()->getName();

        if(!array_key_exists($class_name, $this->annotators)) {
            $this->annotators[$class_name] = new Annotator($metadata);
        }

        return $this->annotators[$class_name];
    }
}