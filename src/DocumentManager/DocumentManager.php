<?php

namespace ODM\DocumentManager;

use ODM\Document\Document;
use ODM\Exception\MappingException;
use ODM\Mapping\DataMapper;
use ODM\DBAL;
use ODM\Mapping\DataMapperFactory;
use ODM\Query\Query;

class DocumentManager
{
    /**
     * @var DBAL
     */
    private $dbal;

    /**
     * @var DataMapper
     */
    private $mapper;

    /**
     * @var string
     */
    private $collection_name;

    /**
     * DocumentManager constructor.
     * @param DBAL   $dbal
     * @param string $class_name
     * @throws MappingException
     */
    public function __construct(DBAL $dbal, string $class_name)
    {
        $this->dbal   = $dbal;
        $this->mapper = DataMapperFactory::getInstance()->get($class_name);

        $this->collection_name = $this->mapper->getAnnotator()->getCollectionName();

        if (!$this->mapper->getAnnotator()->isCollection()) {

            throw new MappingException('There is no collection name for class ' . $class_name);
        }
    }

    /**
     * @param Document $obj
     * @return Document
     */
    public function insert(Document $obj)
    {
        $result = $this->dbal->insert(
            $this->collection_name,
            $this->mapper->objectToArray($obj)
        );

        return $obj->setId($result->getInsertedId());
    }

    /**
     * @param Document $obj
     * @return Document
     */
    public function update(Document $obj)
    {
        $this->dbal->update(
            $this->collection_name,
            [DataMapper::MONGO_ID => $obj->getId()],
            $this->mapper->objectToArray($obj)
        );

        return $obj;
    }

    /**
     * @param $obj
     * @return \MongoDB\DeleteResult
     */
    public function delete(Document $obj)
    {
        return $this->dbal->delete(
            $this->collection_name,
            [DataMapper::MONGO_ID => $obj->getId()]
        );
    }

    /**
     * @param array $filter
     * @param array $options
     * @return Document[]
     */
    public function find(array $filter = [], array $options = [])
    {
        $result = [];
        foreach ($this->dbal->find($this->collection_name, $filter, $options) as $r) {
            $result[] = $this->mapper->arrayToObject((array)$r);
        }

        return $result;
    }

    /**
     * @param array $filter
     * @param array $options
     * @return null|Document
     */
    public function findOne(array $filter = [], array $options = [])
    {
        $data = $this->dbal->findOne($this->collection_name, $filter, $options);

        return empty($data) ? null : $this->mapper->arrayToObject((array)$data);
    }

    /**
     * @param array $filter
     * @param array $options
     * @return Query
     */
    public function createQuery(array $filter = [], array $options = [])
    {
        return (new Query($this))
            ->setFilter($filter)
            ->setOptions($options);
    }
}