<?php
class Vps_Component_Model_Row extends Vps_Model_Row_Abstract
{
    protected $_data;
    
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
        if (isset($this->_data->$name)) {
            $ret = $this->_data->$name;
            if ($name == 'tags') $ret = implode(',', $ret);
            return $ret;
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->_data->$name = $value;
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
        $row->visible = $this->visible;
        $row->name = $this->name;
        $row->is_home = $this->is_home;
        $ret = $row->save();
        if ($id) {
            $this->_afterUpdate();
        } else {
            $this->_afterInsert();
        }
        $this->_afterSave();
        return $ret;
    }

    public function delete()
    {
        $this->_beforeDelete();
        $this->_data->row->delete();
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