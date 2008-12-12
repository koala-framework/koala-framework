<?php
class Vps_Component_Model_Row extends Vps_Model_Row_Abstract
{
    protected $_data;

    public function __construct(array $config)
    {
        $this->_data = $config['data'];
/*
        $m = new Vps_Dao_Pages();
        if (isset($this->_data->row) && $row = $m->find($this->_data->row->id)->current()) {
            $this->parent_id = $row->parent_id;
            $this->pos = $row->pos;
            $this->visible = $row->visible;
            $this->name = $row->name;
            $this->is_home = $row->is_home;
        }
*/
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
        if (isset($this->parent_id)) $row->parent_id = $this->parent_id;
        if (isset($this->pos)) $row->pos = $this->pos;
        if (isset($this->visible)) $row->visible = $this->visible;
        if (isset($this->name)) $row->name = $this->name;
        if (isset($this->is_home)) $row->is_home = $this->is_home;
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