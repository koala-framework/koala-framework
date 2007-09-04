<?php
class Vps_Collection implements ArrayAccess, IteratorAggregate
{
    private $_array = array();

    public function offsetExists($offset)
    {
        foreach ($this->_array as $v) {
            if ($ret = $v->getByName($name)) {
                return true;
            }
        }
        return false;
    }

    public function getByName($name)
    {
        foreach ($this->_array as $v) {
            if ($ret = $v->getByName($name)) {
                return $ret;
            }
        }
        throw new Vps_Exception("Item '$name' not found.");
    }

    public function offsetGet($offset)
    {
        return $this->getByName($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Vps_Collection_Item_Interface) {
            throw new Vps_Exception("Vps_Collection can hold only items with Vps_Collection_Item_Interface");
        }
        if (is_null($offset)) {
            $this->add($value);
        } else {
            throw new Vps_Exception("Not yet Implemented.");
        }
    }

    public function add(Vps_Collection_Item_Interface $value)
    {
        $this->_array[] = $value;
        $this->_postInsertValue($value);
        return $value;
    }

    public function offsetUnset($offset)
    {
        throw new Vps_Exception("Not yet Implemented.");
    }

    public function getIterator()
    {
        return new Vps_Collection_Iterator($this);
    }

    public function getRecursiveIterator()
    {
        return new RecursiveIteratorIterator(
                        new Vps_Collection_Iterator_Recursive($this));
    }

    public function first()
    {
        if (isset($this->_array[0])) {
            return $this->_array[0];
        } else {
            return null;
        }
    }

    public function prepend(Vps_Collection_Item_Interface$value)
    {
        array_unshift($this->_array, $value);
        $this->_postInsertValue($value);
        return $value;
    }

    public function append(Vps_Collection_Item_Interface$value)
    {
        return $this->add($value);
    }

    public function insertBefore($where, Vps_Collection_Item_Interface $value)
    {
        foreach ($this->_array as $i=>$v) {
            if ($v->getName() == $where) {
                array_splice($this->_array, $i, 0, array($value));
            }
        }
        $this->_postInsertValue($value);
        return $value;
    }

    public function getArray()
    {
        return $this->_array;
    }

    protected function _postInsertValue($value)
    {
    }
}
