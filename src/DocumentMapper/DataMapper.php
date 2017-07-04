<?php

namespace ODM\DocumentMapper;

use ODM\DBAL;
use ODM\Document\Document;
use ODM\Instantiator\Instantiator;

class DataMapper
{
    private $dbal;

    private $table_name;
    private $class_name;
    private $instantiator;

    const MONGO_ID = '_id';
    const ID       = 'id';

    /**
     * DataMapper constructor.
     * @param DBAL   $dbal
     * @param string $class_name
     */
    public function __construct(DBAL $dbal, string $class_name)
    {
        $this->dbal         = $dbal;
        $this->class_name   = $class_name;
        $path               = explode('\\', $class_name);
        $this->table_name   = mb_strtolower($this->camelCaseToSnake(array_pop($path)));
        $this->instantiator = new Instantiator();
    }

    /**
     * @param Document $obj
     * @return Document
     */
    public function insert(Document $obj)
    {
        $data = $this->objToArray($obj);

        if (array_key_exists(self::MONGO_ID, $data) && empty($data[self::MONGO_ID])) {
            $data[self::MONGO_ID] = $this->generateId();
        }

        $result = $this->dbal->insert($this->table_name, $data);

        return $obj->setId($result->getInsertedId());
    }

    /**
     * @param Document $obj
     * @return Document
     */
    public function update(Document $obj)
    {
        $data = $this->objToArray($obj);
        unset($data[self::MONGO_ID]);

        $this->dbal->update($this->table_name, [self::MONGO_ID => $obj->getId()], $data);

        return $obj;
    }

    /**
     * @param Document $obj
     * @return \MongoDB\DeleteResult
     */
    public function delete(Document $obj)
    {
        return $this->dbal->delete($this->table_name, [self::MONGO_ID => $obj->getId()]);
    }

    /**
     * @return bool
     */
    public function drop()
    {
        return (bool)$this->dbal->drop($this->table_name);
    }

    /**
     * @param array $filter
     * @param array $options
     * @return Document[]|array
     */
    public function find(array $filter = [], array $options = [])
    {
        if (array_key_exists(self::ID, $filter)) {
            $filter[self::MONGO_ID] = $filter[self::ID];
            unset($filter[self::ID]);
        }

        $result = [];
        foreach ($this->dbal->find($this->table_name, $filter, $options) as $r) {
            $result[] = $this->mapObj($this->class_name, (array)$r);
        }

        return $result;
    }

    /**
     * @param array $filter
     * @param array $options
     * @return Document|null
     */
    public function findOne(array $filter = [], array $options = [])
    {
        if (array_key_exists(self::ID, $filter)) {
            $filter[self::MONGO_ID] = $filter[self::ID];
            unset($filter[self::ID]);
        }

        $data = $this->dbal->findOne($this->table_name, $filter, $options);

        return empty($data) ? null : $this->mapObj($this->class_name, (array)$data);
    }

    /**
     * @return string
     */
    private function generateId()
    {
        return bin2hex(random_bytes(15));
    }

    /**
     * @param string $class_name
     * @param array  $data
     * @return Document
     */
    private function mapObj(string $class_name, array $data)
    {
        $obj     = $this->instantiator->instantiate($class_name);
        $reflect = new \ReflectionClass($obj);
        $parent_properties = null !== $reflect->getParentClass() ? $reflect->getParentClass()->getProperties() : [];
        $properties = array_merge($reflect->getProperties(), $parent_properties);

        foreach($properties  as $prop) {

            if ($prop->getName() === self::ID) {
                $prop_name = self::MONGO_ID;
            } else {
                $prop_name = $prop->getName();
            }

            if (!array_key_exists($prop_name, $data)) {
                continue;
            }

            if ($prop->isPrivate() || $prop->isProtected()) {
                $prop->setAccessible(true);
            }

            if ($prop->getName() === self::ID) {
                $prop->setValue($obj, (string)$data[$prop_name]);

                continue;
            }

            $prop->setValue($obj, $data[$prop_name]);
        }

        return $obj;
    }

    /**
     * @param $input
     * @return string
     */
    private function camelCaseToSnake(string $input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $res = $matches[0];
        foreach ($res as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $res);
    }

    /**
     * @param Document $obj
     * @return array
     */
    private function objToArray(Document $obj)
    {
        $data    = [];
        $reflect = new \ReflectionClass($obj);
        $parent_properties = null !== $reflect->getParentClass() ? $reflect->getParentClass()->getProperties() : [];
        $properties = array_merge($reflect->getProperties(), $parent_properties);

        foreach ($properties as $prop) {

            if ($prop->getName() === self::ID) {
                $prop_name = self::MONGO_ID;
            } else {
                $prop_name = $prop->getName();
            }

            if ($prop->isPrivate() || $prop->isProtected()) {
                $prop->setAccessible(true);
            }

            $data[$prop_name] = $prop->getValue($obj);
        }

        return $data;
    }
}