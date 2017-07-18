<?php

namespace ODM\Mapping\Instantiator;

class Instantiator
{
    /**
     * @param string $class_name
     * @return mixed
     */
    public function getInstance(string $class_name)
    {
        $serialized_string = sprintf(
            'O:%d:"%s":0:{}',
            strlen($class_name),
            $class_name
        );

        return unserialize($serialized_string);
    }
}