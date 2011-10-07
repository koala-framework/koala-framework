<?php
class Vps_Component_Generator_Model_Row extends Vps_Model_Row_Abstract
{
    protected $_data;
    protected $_model;
    
    public function __construct(array $config)
    {
        $this->_data = $config['data'];
        $this->_model = $config['model'];
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
            return $this->_data->$name;
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
        if (isset($this->visible)) {
            $row = $this->_model->getTable()->find($this->componentId)->current();
            $row->visible = $this->visible;
            $row->save();
        }
    }

    public function delete()
    {
        $m = Vps_Model_Abstract::getInstance('Vpc_Root_Category_GeneratorModel');
        $row = $m->getRow($this->componentId)->current();
        $row->delete();
    }
    
    public function getData()
    {
        return $this->_data;
    }

    public function toArray()
    {
        return array(
            'isPage' => $this->_data->isPage
        );
    }
}