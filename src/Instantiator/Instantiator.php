<?php

namespace ODM\Instantiator;

class Instantiator
{
    const SERIALIZATION_FORMAT_USE_UNSERIALIZER   = 'C';
    const SERIALIZATION_FORMAT_AVOID_UNSERIALIZER = 'O';

    /**
     * @param string $class_name
     * @return mixed
     */
    public function instantiate(string $class_name)
    {
        $serialized_string = sprintf(
            'O:%d:"%s":0:{}',
            strlen($class_name),
            $class_name
        );

        return unserialize($serialized_string);
    }
}