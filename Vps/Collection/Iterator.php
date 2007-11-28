<?php
class Vps_Collection_Iterator extends ArrayIterator
{
    private $_collection;

    public function __construct($collection, $flags = 0)
    {
        $this->_collection = $collection;
        parent::__construct($collection->getArray(), $flags);
    }
}
