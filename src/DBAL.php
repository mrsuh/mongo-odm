<?php

namespace ODM;

use MongoDB\Client;

class DBAL
{
    private $db;

    public function __construct($host, $port, $db_name)
    {
        $this->db = (new Client("mongodb://$host:$port"))->$db_name;
    }

    /**
     * @param       $table_name
     * @param array $data
     * @return mixed
     */
    public function insert($table_name, array $data)
    {
        return $this->db->$table_name->insertOne($data);
    }

    /**
     * @param       $table_name
     * @param array $data
     * @return mixed
     */
    public function insertMany($table_name, array $data)
    {
        return $this->db->$table_name->insertMany($data);
    }

    /**
     * @param       $table_name
     * @param array $filter
     * @param array $data
     * @return mixed
     */
    public function update($table_name, array $filter, array $data)
    {
        return $this->db->$table_name->updateOne($filter, ['$set' => $data]);
    }

    /**
     * @param       $table_name
     * @param array $filter
     * @return mixed
     */
    public function delete($table_name, array $filter)
    {
        return $this->db->$table_name->deleteOne($filter);
    }

    /**
     * @param       $table_name
     * @param array $filter
     * @param array $options
     * @return mixed
     */
    public function find($table_name, array $filter, array $options = [])
    {
        return $this->db->$table_name->find($filter, $options);
    }

    /**
     * @param       $table_name
     * @param array $filter
     * @param array $options
     * @return mixed
     */
    public function findOne($table_name, array $filter, array $options = [])
    {
        return $this->db->$table_name->findOne($filter, $options);
    }

    /**
     * @param $table_name
     * @return mixed
     */
    public function drop($table_name)
    {
        return $this->db->$table_name->drop();
    }
}

