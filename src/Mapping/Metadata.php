<?php

namespace ODM\Mapping;

use ODM\Mapping\Instantiator\Instantiator;

class Metadata
{
    /**
     * @var \ReflectionProperty[]
     */
    private $properties;

    /**
     * @var \ReflectionClass
     */
    private $class;

    /**
     * Metadata constructor.
     * @param string $class_name
     */
    public function __construct(string $class_name)
    {
        $instantiator = new Instantiator();

        $document = $instantiator->getInstance($class_name);

        $this->class = new \ReflectionClass($document);

        $this->properties = $this->class->getProperties();
        $class = $this->class;
        while($parent = $class->getParentClass()) {
            $this->properties = array_merge($this->properties, $parent->getProperties());
            $class = $parent;
        }
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass()
    {
        return $this->class;
    }

    /**
     * @return \ReflectionProperty[]
     */
    public function getReflectionProperties()
    {
        return $this->properties;
    }
}