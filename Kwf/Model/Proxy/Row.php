<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Proxy_Row extends Kwf_Model_Row_Abstract
{
    protected $_row;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
        parent::__construct($config);
    }

    public function getProxiedRow()
    {
        return $this->_row;
    }

    public function __isset($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            return parent::__isset($name);
        } else if ($this->_row->hasColumn($name)) {
            return true;
        } else {
            return parent::__isset($name);
        }
    }

    public function __unset($name)
    {
        if ($this->_row->hasColumn($name)) {
            $this->_row->__unset($name);
        } else {
            parent::__unset($name);
        }
    }

    public function __get($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            return parent::__get($name);
        } else if ($this->_row->hasColumn($name)) {
            return $this->_row->$name;
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if ($this->_row->hasColumn($name)) {
            $this->_row->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }


    //für forceSave
    protected function _setDirty($column)
    {
        $this->_row->_setDirty($column);
    }

    protected function _resetDirty()
    {
        parent::_resetDirty();
        $this->_row->_resetDirty();
    }

    protected function _isDirty($column = null)
    {
        return $this->_row->isDirty($column);
    }

    public function getDirtyColumns()
    {
        $ret = $this->_row->getDirtyColumns();
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($ret, $r->getDirtyColumns());
        }
        return $ret;
    }

    public function getCleanValue($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            return parent::getCleanValue($name);
        } else if ($this->_row->hasColumn($name)) {
            return $this->_row->getCleanValue($name);
        } else {
            return parent::getCleanValue($name);
        }
    }

    protected function _saveWithoutResetDirty()
    {
        $this->_beforeSave();
        $id = $this->getCleanValue($this->_getPrimaryKey());
        if (!$id) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSaveSiblingMaster();
        $ret = $this->_row->_saveWithoutResetDirty();
        parent::_saveWithoutResetDirty();
        $this->_afterSave();
        if (!$id) {
            $this->_afterInsert();
        } else {
            $this->_afterUpdate();
        }
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
        $ret = array_merge(
            parent::toArray(),
            $this->_row->toArray()
        );
        return $ret;
    }

    protected function _toArrayWithoutPrimaryKeys()
    {
        $ret = $this->_row->_toArrayWithoutPrimaryKeys();
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($r->_toArrayWithoutPrimaryKeys(), $ret);
        }
        return $ret;
    }

    //proxy must not fire events itself, it re-fires source model events
    protected function _callObserver($fn)
    {
    }
}
