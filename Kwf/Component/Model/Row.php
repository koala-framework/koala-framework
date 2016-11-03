<?php
class Kwf_Component_Model_Row extends Kwf_Model_Row_Abstract
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
        $fields = array('parent_id', 'pos', 'visible', 'is_home');
        if (!$this->_tableLoaded &&
            in_array($name, $fields) &&
            is_numeric($this->componentId)
        ) {
            $m = $this->_data->generator->getModel();
            if (isset($this->_data->row) && $row = $m->getRow($this->_data->row->id)) {
                foreach ($fields as $field) {
                    $this->_data->$field = $row->$field;
                }
            }
            $this->_tableLoaded = true;
        }
        if ($name == 'id') $name = 'componentId';
        if (isset($this->_data->$name)) {
            return $this->_data->$name;;
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


    protected function _saveWithoutResetDirty()
    {
        $this->_beforeSave();

        if (!$this->_setValues) return; //nothing changed

        if ($this->_data instanceof Kwf_Component_Data_Root) {
            return;
        } else if ($this->_data->generator instanceof Kwc_Root_Category_Generator) {
            $id = $this->_data->dbId;
            $row = $this->_data->generator->getModel()->getRow($id);
        } else {
            $m = $this->_data->row->getModel();
            $primaryKey = $m->getPrimaryKey();
            $id = $this->_data->row->$primaryKey;
            if ($id) {
                $this->_beforeUpdate();
                $row = $this->_data->row;
            } else {
                $this->_beforeInsert();
                $row = $m->createRow();
            }
        }
        foreach ($this->_setValues as $key) {
            if ($key == 'visible' && !($this->_data->generator instanceof Kwc_Root_Category_Generator)) {
                $this->_data->generator->setVisible($this->_data, $this->_data->visible);
            } else {
                $row->$key = $this->_data->$key;
            }
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
        Kwf_Component_Data_Root::reset();
        Kwf_Component_Generator_Abstract::clearInstances();
    }

    public function delete()
    {
        $this->_beforeDelete();
        $m = $this->_data->generator->getModel();
        $m->getRow($this->_data->row->id)->delete();
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
