<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Union_Row extends Kwf_Model_Row_Abstract
{
    protected $_sourceRow;
    protected $_id;

    public function __construct(array $config)
    {
        $this->_sourceRow = $config['sourceRow'];
        $this->_id = $config['id'];
        parent::__construct($config);
    }

    public function getSourceRow()
    {
        return $this->_sourceRow;
    }

    public function __isset($name)
    {
        if ($name == 'id') return true;
        $mapping = $this->_model->getUnionColumnMapping();
        $name = $this->_sourceRow->getModel()->getColumnMapping($mapping, $name);
        return $this->_sourceRow->__isset($value);
    }

    public function __unset($name)
    {
        if ($name == 'id') throw new Kwf_Exception('unable to unset id');
        $mapping = $this->_model->getUnionColumnMapping();
        $name = $this->_sourceRow->getModel()->getColumnMapping($mapping, $name);
        $this->_sourceRow->__unset($value);
    }

    public function __get($name)
    {
        if ($name == 'id') return $this->_id;
        $mapping = $this->_model->getUnionColumnMapping();
        $name = $this->_sourceRow->getModel()->getColumnMapping($mapping, $name);
        return $this->_sourceRow->$name;
    }

    public function __set($name, $value)
    {
        if ($name == 'id') throw new Kwf_Exception('unable to change id');
        $mapping = $this->_model->getUnionColumnMapping();
        $name = $this->_sourceRow->getModel()->getColumnMapping($mapping, $name);
        $this->_sourceRow->$name = $value;
    }


    //für forceSave
    protected function _setDirty($column)
    {
        $this->_sourceRow->_setDirty($column);
    }

    protected function _resetDirty()
    {
        parent::_resetDirty();
        $this->_sourceRow->_resetDirty();
    }

    protected function _isDirty()
    {
        return $this->_sourceRow->isDirty();
    }

    public function getDirtyColumns()
    {
        $ret = $this->_sourceRow->getDirtyColumns();
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($ret, $r->getDirtyColumns());
        }
        return $ret;
    }

    public function getCleanValue($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            return parent::getCleanValue($name);
        } else if ($this->_sourceRow->hasColumn($name)) {
            return $this->_sourceRow->getCleanValue($name);
        } else {
            return parent::getCleanValue($name);
        }
    }

    protected function _saveWithoutResetDirty()
    {
        $this->_beforeSave();
        $id = $this->{$this->_getPrimaryKey()};
        if (!$id) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSaveSiblingMaster();
        $ret = $this->_sourceRow->_saveWithoutResetDirty();
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
        $this->_sourceRow->delete();
        $this->_afterDelete();
    }

    public function toArray()
    {
        $ret = array_merge(
            parent::toArray(),
            $this->_sourceRow->toArray()
        );
        return $ret;
    }

    protected function _toArrayWithoutPrimaryKeys()
    {
        $ret = $this->_sourceRow->_toArrayWithoutPrimaryKeys();
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($r->_toArrayWithoutPrimaryKeys(), $ret);
        }
        return $ret;
    }

    //union must not fire events itself, it re-fires source model events
    protected function _callObserver($fn)
    {
    }
}
