<?php
class Vps_Model_Row_Data_Abstract extends Vps_Model_Row_Abstract
{
    protected $_data = array();
    protected $_cleanData = array();
    
    public function __construct(array $config)
    {
        $this->_data = (array)$config['data'];
        $this->_cleanData = $this->_data;
        parent::__construct($config);
    }

    public function __isset($name)
    {
        if ($this->_model->getColumns() && !in_array($name, $this->_model->getColumns())) {
            return parent::__isset($name);
        } else {
            return isset($this->_data[$name]);
        }
    }

    public function __unset($name)
    {
        if ($this->_model->getColumns() && !in_array($name, $this->_model->getColumns())) {
            parent::__unset($name);
        } else {
            unset($this->_row->$name);
        }
    }

    public function __get($name)
    {
        if ($this->_model->getColumns() && !in_array($name, $this->_model->getColumns())) {
            return parent::__get($name);
        } else {
            if (!isset($this->_data[$name])) return null;
            return $this->_data[$name];
        }
    }
    
    public function __set($name, $value)
    {
        if ($this->_model->getColumns() && !in_array($name, $this->_model->getColumns())) {
            parent::__set($name, $value);
            return;
        }
        $this->_data[$name] = $value;
        $this->_postSet($name, $value);
    }

    public function toArray()
    {
        return $this->_data;
    }

    public function save()
    {
        parent::save();

        if (isset($this->_cleanData[$this->_getPrimaryKey()])) {
            $this->_beforeInsert();
            $this->_beforeSave();
            $id = $this->_cleanData[$this->_getPrimaryKey()];
            $ret = $this->_model->update($id, $this, $this->_data);
            $this->_afterInsert();
        } else {
            $this->_beforeSave();
            $ret = $this->_model->insert($this, $this->_data);
        }

        $this->_refresh($ret);

        $this->_afterSave();

        return $ret;
    }

    protected function _refresh($id)
    {
        $this->_data = $this->_model->getRow($id)->_data;
        $this->_cleanData = $this->_data;
    }

    public function delete()
    {
        parent::delete();

        $this->_beforeDelete();
        $id = $this->{$this->_getPrimaryKey()};
        $this->_model->delete($id, $this);
        $this->_afterDelete();
    }
}
