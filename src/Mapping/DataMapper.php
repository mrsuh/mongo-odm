<?php

namespace ODM\Mapping;

use ODM\Exception\MappingException;
use ODM\Mapping\Annotator\Annotator;
use ODM\Mapping\Annotator\AnnotatorFactory;
use ODM\Mapping\Instantiator\Instantiator;

class DataMapper
{
    const MONGO_ID = '_id';
    const ID       = 'id';

    const TO_OBJECT = 1;
    const TO_ARRAY  = 2;

    /**
     * @var Instantiator
     */
    private $instantiator;

    /**
     * @var Annotator
     */
    private $annotator;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var string
     */
    private $class_name;

    /**
     * DataMapper constructor.
     */
    public function __construct(string $class_name)
    {
        $this->class_name   = $class_name;
        $this->instantiator = new Instantiator();
        $this->metadata     = MetadataFactory::getInstance()->get($class_name);
        $this->annotator    = AnnotatorFactory::getInstance()->get($this->metadata);
    }

    /**
     * @return Annotator
     */
    public function getAnnotator(): Annotator
    {
        return $this->annotator;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function arrayToObject(array $data)
    {
        $obj = $this->instantiator->getInstance($this->class_name);

        $properties = $this->metadata->getReflectionProperties();

        foreach ($properties as $prop) {

            if (!$this->annotator->hasField($prop)) {
                continue;
            }

            $annotation_name = $this->annotator->getFieldName($prop);
            $annotation_type = $this->annotator->getFieldType($prop);

            if (!array_key_exists($annotation_name, $data)) {
                continue;
            }

            if ($prop->isPrivate() || $prop->isProtected()) {
                $prop->setAccessible(true);
            }

            $value = $this->typeCast($annotation_type, $data[$annotation_name], self::TO_OBJECT);

            $prop->setValue($obj, $value);
        }

        return $obj;
    }

    /**
     * @param $obj
     * @return array
     */
    public function objectToArray($obj)
    {
        $data = [];

        $properties = $this->metadata->getReflectionProperties();

        foreach ($properties as $prop) {

            if (!$this->annotator->hasField($prop)) {

                continue;
            }

            $annotation_name = $this->annotator->getFieldName($prop);
            $annotation_type = $this->annotator->getFieldType($prop);

            if ($prop->isPrivate() || $prop->isProtected()) {
                $prop->setAccessible(true);
            }

            $raw_value = $prop->getValue($obj);

            if (self::MONGO_ID === $annotation_name && empty($raw_value)) {
                continue;
            }

            $value = $this->typeCast($annotation_type, $raw_value, self::TO_ARRAY);

            $data[$annotation_name] = $value;
        }

        return $data;
    }

    /**
     * @param string $type
     * @param        $value
     * @param int    $to
     * @return array|float|int|string|mixed
     * @throws MappingException
     */
    private function typeCast(string $type, $value, int $to)
    {
        switch ($type) {
            case 'integer':
                $value = (integer)$value;
                break;
            case 'string':
                $value = (string)$value;
                break;
            case 'float':
                $value = (float)$value;
                break;
            case 'array':
                $value = (array)$value;
                break;
            case 'integer[]':
                if (!is_array($value)) {
                    $value = (array)$value;
                };
                array_walk($value, function (&$val) {
                    $val = (integer)$val;
                });
                break;
            case 'string[]':
                if (!is_array($value)) {
                    $value = (array)$value;
                };
                array_walk($value, function (&$val) {
                    $val = (string)$val;
                });
                break;
            case 'float[]':
                if (!is_array($value)) {
                    $value = (array)$value;
                };
                array_walk($value, function (&$val) {
                    $val = (float)$val;
                });
                break;
            default:

                if (1 === preg_match('/^(.*)\[\]$/', $type, $match)) {
                    $class_name = $match[1];

                    if (!class_exists($class_name)) {

                        throw new MappingException('Class not exists ' . $class_name);
                    }

                    if (!is_array($value)) {
                        $value = (array)$value;
                    };

                    $mapper = DataMapperFactory::getInstance()->get($class_name);

                    array_walk($value, function (&$val) use ($mapper, $to) {

                        if (self::TO_OBJECT === $to) {
                            $val = $mapper->arrayToObject((array)$val);
                        } else {
                            $val = $mapper->objectToArray($val);
                        }

                    });

                    break;
                }

                if (1 === preg_match('/^(.*)$/', $type, $match)) {
                    $class_name = $match[1];

                    if (!class_exists($class_name)) {

                        throw new MappingException('Class not exists ' . $class_name);
                    }

                    $mapper = DataMapperFactory::getInstance()->get($class_name);

                    if (self::TO_OBJECT === $to) {
                        $value = $mapper->arrayToObject((array)$value);
                    } else {
                        $value = $mapper->objectToArray($value);
                    }

                    break;
                }

                throw new MappingException('Invalid type ' . $type);
        }

        return $value;
    }
}