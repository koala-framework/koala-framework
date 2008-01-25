<?php
class Vps_Auto_Data_Vpc_Image extends Vps_Auto_Data_Abstract
{
    protected $_class;
    protected $_componentId;

    public function __construct($class, $componentId)
    {
        $this->_class = $class;
        $this->_componentId = $componentId;
    }

    public function load($row)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        $table = new $tablename(array('componentClass'=>$this->_class));
        $componentId = $this->_componentId . '-' . $row->id;
        $row = $table->find($componentId)->current();
        if ($row) {
            return '<img src="' . $row->getFileUrl(null, 'mini') . '" />';
        } else {
            return '';
        }
    }
}
