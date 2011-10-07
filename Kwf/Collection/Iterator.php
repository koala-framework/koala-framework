<?php
class Vps_Collection_Iterator extends ArrayIterator
{
    private $_collection;

    public function __construct($collection, $flags = 0)
    {
        $this->_collection = $collection;
        if ($collection instanceof Vps_Collection) {
            $collection = $collection->getArray();
        }
        parent::__construct($collection, $flags);
    }
}
