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
            return true;
        }
    }

    public function __unset($name)
    {
        if ($this->_model->getColumns() && !in_array($name, $this->_model->getColumns())) {
            parent::__unset($name);
        } else if (isset($this->_data[$name])) {
            unset($this->_data[$name]);
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
            $ret = $this->_model->update($this, $this->_data);
            $this->_afterInsert();
        } else {
            $this->_beforeSave();
            $ret = $this->_model->insert($this, $this->_data);
        }
        $this->_cleanData = $this->_data;

        $this->_afterSave();

        return $ret;
    }

    public function delete()
    {
        parent::delete();

        $this->_beforeDelete();
        $id = $this->{$this->_getPrimaryKey()};
        $this->_model->delete($this);
        $this->_afterDelete();

        $this->_data = array_combine(
            array_keys($this->_data),
            array_fill(0, count($this->_data), null)
        );
    }

}
