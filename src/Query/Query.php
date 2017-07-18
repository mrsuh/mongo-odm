<?php

namespace ODM\Query;

use ODM\DocumentMapper\DocumentManager;

class Query
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var array
     */
    private $filter;

    /**
     * @var array
     */
    private $options;

    /**
     * Query constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @return array|\ODM\Document\Document[]
     */
    public function getResult()
    {
        return $this->dm->find($this->filter, $this->options);
    }

    /**
     * @return null|\ODM\Document\Document
     */
    public function getOneOrNullResult()
    {
        return $this->dm->findOne($this->filter, $this->options);
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->options['order'] = $order;

        return $this;
    }

    /**
     * @param $skip
     * @return $this
     */
    public function setSkip($skip)
    {
        $this->options['skip'] = $skip;

        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->options['limit'] = $limit;

        return $this;
    }
}