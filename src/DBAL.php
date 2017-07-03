<?php

namespace ODM;

use MongoDB\Client;

class DBAL
{
    private $db;

    /**
     * DBAL constructor.
     * @param string $host
     * @param string $port
     * @param string $db_name
     */
    public function __construct(string $host, string $port, string $db_name)
    {
        $this->db = (new Client("mongodb://$host:$port"))->$db_name;
    }

    /**
     * @param string $table_name
     * @param array  $data
     * @return \MongoDB\InsertOneResult
     */
    public function insert(string $table_name, array $data)
    {
        return $this->db->$table_name->insertOne($data);
    }

    /**
     * @param string $table_name
     * @param array  $data
     * @return \MongoDB\InsertManyResult
     */
    public function insertMany(string $table_name, array $data)
    {
        return $this->db->$table_name->insertMany($data);
    }

    /**
     * @param string $table_name
     * @param array  $filter
     * @param array  $data
     * @return \MongoDB\UpdateResult
     */
    public function update(string $table_name, array $filter, array $data)
    {
        return $this->db->$table_name->updateOne($filter, ['$set' => $data]);
    }

    /**
     * @param string $table_name
     * @param array  $filter
     * @return \MongoDB\DeleteResult
     */
    public function delete(string $table_name, array $filter)
    {
        return $this->db->$table_name->deleteOne($filter);
    }

    /**
     * @param string $table_name
     * @param array  $filter
     * @param array  $options
     * @return \MongoDB\Driver\Cursor
     */
    public function find(string $table_name, array $filter, array $options = [])
    {
        return $this->db->$table_name->find($filter, $options);
    }

    /**
     * @param string $table_name
     * @param array  $filter
     * @param array  $options
     * @return array|null|object
     */
    public function findOne(string $table_name, array $filter, array $options = [])
    {
        return $this->db->$table_name->findOne($filter, $options);
    }

    /**
     * @param string $table_name
     * @return array|object
     */
    public function drop(string $table_name)
    {
        return $this->db->$table_name->drop();
    }
}

