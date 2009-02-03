<?php
class Vps_Model_Row_Item_Abstract extends Vps_Model_Row_Abstract
{
    protected $_item;

    public function __construct(array $config)
    {
        $this->_item = $config['item'];
        parent::__construct($config);
    }

    public function getItem()
    {
        return $this->_item;
    }

    public function __isset($name)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            return parent::__isset($name);
        } else {
            return true;
        }
    }

    public function __unset($name)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            parent::__unset($name);
        } else {
            $name = $this->_transformColumnName($name);
            unset($this->_item->$name);
        }
    }

    public function __get($name)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            return parent::__get($name);
        } else {
            $name = $this->_transformColumnName($name);
            if (isset($this->_item->$name)) {
                return $this->_item->$name;
            } else {
                return null;
            }
        }
    }

    public function __set($name, $value)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            parent::__set($name, $value);
            return;
        }
        $n = $this->_transformColumnName($name);
        $this->_item->$n = $value;
        $this->_postSet($name, $value);
    }

    public function toArray()
    {
        $ret = array();
        foreach ($this->_model->getOwnColumns() as $c) {
            $ret[$c] = $this->$c;
        }
        return $ret;
    }
}
