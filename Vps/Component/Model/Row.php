<?php
class Vps_Component_Model_Row extends Vps_Model_Row_Abstract
{
    protected $_data;
    private $_tableLoaded = false;

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
        if (isset($this->_data->parent_id)) $row->parent_id = $this->_data->parent_id;
        if (isset($this->_data->pos)) $row->pos = $this->_data->pos;
        if (isset($this->_data->visible)) $row->visible = $this->_data->visible;
        if (isset($this->_data->name)) $row->name = $this->_data->name;
        if (isset($this->_data->is_home)) $row->is_home = $this->_data->is_home;
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