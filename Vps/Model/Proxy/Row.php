<?php
class Vps_Model_Proxy_Row extends Vps_Model_Row_Abstract
{
    protected $_row;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
        parent::__construct($config);
    }

    public function __isset($name)
    {
        if ($this->_row->getModel()->hasColumn($name)) {
            return isset($this->_row->$name);
        } else {
            return parent::__isset($name);
        }
    }

    public function __unset($name)
    {
        if ($this->_row->getModel()->hasColumn($name)) {
            $this->_row->__unset($name);
        } else {
            parent::__unset($name);
        }
    }

    public function __get($name)
    {
        if ($this->_row->getModel()->hasColumn($name)) {
            return $this->_row->$name;
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if ($this->_row->getModel()->hasColumn($name)) {
            $this->_row->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function save()
    {
        $id = $this->{$this->_getPrimaryKey()};
        if (!$id) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSave();
        $ret = $this->_row->save();
        if (!$id) {
            $this->_afterInsert();
        } else {
            $this->_afterUpdate();
        }
        $this->_afterSave();
        parent::save();
        return $ret;
    }

    public function delete()
    {
        parent::delete();
        $this->_beforeDelete();
        $this->_row->delete();
        $this->_afterDelete();
    }

    public function toArray()
    {
        return $this->_row->toArray();
    }
}
