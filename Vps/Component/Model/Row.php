<?php
class Vps_Component_Model_Row extends Vps_Model_Row_Abstract
{
    protected $_data;
    private $_tableLoaded = false;
    private $_setValues = array();

    public function __construct(array $config)
    {
        $this->_data = $config['data'];
        parent::__construct($config);
    }

    public function __isset($name)
    {
        return isset($this->_data->$name);
    }

    public function __unset($name)
    {
        unset($this->_data->$name);
    }

    public function __get($name)
    {
        $fields = array('parent_id', 'pos', 'visible', 'name', 'is_home');
        if (!$this->_tableLoaded &&
            in_array($name, $fields) &&
            is_numeric($this->componentId)
        ) {
            $m = new Vps_Dao_Pages();
            if (isset($this->_data->row) && $row = $m->find($this->_data->row->id)->current()) {
                foreach ($fields as $field) {
                    $this->_data->$field = $row->$field;
                }
            }
            $this->_tableLoaded = true;
        }
        if ($name == 'id') $name = 'componentId';
        if (isset($this->_data->$name)) {
            $ret = $this->_data->$name;
            if ($name == 'tags') $ret = implode(',', $ret);
            return $ret;
        } else if ($name == 'parent_id' && $this->_data->parent) {
            return $this->_data->parent->componentId;
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->_data->$name = $value;
        $this->_setValues[] = $name;
    }


    public function save()
    {
        $this->_beforeSave();
        $id = $this->_data->row->id;
        $m = new Vps_Dao_Pages();
        if ($id) {
            if (!is_numeric($id)) {
                throw new Vps_Exception("Can only save pages");
            }
            $this->_beforeUpdate();
            $row = $m->find($id)->current();
        } else {
            $this->_beforeInsert();
            $row = $m->createRow();
        }
        foreach ($this->_setValues as $key) {
            $row->$key = $this->_data->$key;
        }
        $ret = $row->save();
        if ($id) {
            $this->_afterUpdate();
        } else {
            $this->_afterInsert();
        }
        $this->_afterSave();
        return $ret;
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        Vps_Component_Data_Root::reset();
        Vps_Component_Generator_Abstract::clearInstances();
    }

    public function delete()
    {
        $this->_beforeDelete();
        $m = new Vps_Dao_Pages();
        $m->find($this->_data->row->id)->current()->delete();
        $this->_afterDelete();
    }

    public function getData()
    {
        return $this->_data;
    }

    public function toArray()
    {
        return array();
    }
}