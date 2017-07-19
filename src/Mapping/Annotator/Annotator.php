<?php

namespace ODM\Mapping\Annotator;

use ODM\Mapping\Metadata;

class Annotator
{
    /**
     * @var bool
     */
    private $is_collection;

    /**
     * @var string
     */
    private $collection_name;

    /**
     * @var string[]
     */
    private $field_names;

    /**
     * @var string[]
     */
    private $field_types;

    /**
     * Annotator constructor.
     * @param Metadata $metadata
     */
    public function __construct(Metadata $metadata)
    {
        $this->field_types = [];
        $this->field_names = [];

        $this->parseCollection($metadata->getReflectionClass());

        foreach($metadata->getReflectionProperties() as $property) {
            $this->parseField($property);
        }
    }

    /**
     * @param \ReflectionClass $reflection_class
     * @return bool
     */
    private function parseCollection(\ReflectionClass $reflection_class)
    {
        $pattern = '/@Collection\((.*)\)/';
        preg_match($pattern, $reflection_class->getDocComment(), $matches);

        $doc = array_key_exists(1, $matches) ? $matches[1] : '';
        $name = $this->parseParameter('name', $doc);

        $this->is_collection = '' !== $name;
        $this->collection_name = $name;

        return true;
    }

    /**
     * @param \ReflectionProperty $reflection_property
     * @return bool
     */
    private function parseField(\ReflectionProperty $reflection_property)
    {
        $pattern = '/@Field\((.*)\)/';
        preg_match($pattern, $reflection_property->getDocComment(), $matches);

        $doc = array_key_exists(1, $matches) ? $matches[1] : '';
        $name = $this->parseParameter('name', $doc);
        $type = $this->parseParameter('type', $doc);

        $this->field_names[$reflection_property->getName()] = $name;
        $this->field_types[$reflection_property->getName()] = $type;

        return true;
    }

    /**
     * @param string $parameter
     * @param string $annotation
     * @return string
     */
    private function parseParameter(string $parameter, string $annotation)
    {
        preg_match('/'.preg_quote($parameter).'=\"([^\,\"]+)\"/', $annotation, $matches2);
        return array_key_exists(1, $matches2) ? $matches2[1] : '';
    }

    /**
     * @param \ReflectionProperty $reflection_property
     * @return bool
     */
    public function hasField(\ReflectionProperty $reflection_property)
    {
        return array_key_exists($reflection_property->getName(), $this->field_names);
    }

    /**
     * @param \ReflectionProperty $reflection_property
     * @return string
     */
    public function getFieldName(\ReflectionProperty $reflection_property)
    {
        $name = $reflection_property->getName();
        return array_key_exists($name, $this->field_names) ? $this->field_names[$name] : '';
    }

    /**
     * @param \ReflectionProperty $reflection_property
     * @return string
     */
    public function getFieldType(\ReflectionProperty $reflection_property)
    {
        $name = $reflection_property->getName();
        return array_key_exists($name, $this->field_types) ? $this->field_types[$name] : '';
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return (bool)$this->is_collection;
    }

    /**
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collection_name;
    }
}