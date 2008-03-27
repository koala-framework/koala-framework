<?php
class Vps_Auto_Data_Vpc_Image extends Vps_Auto_Data_Abstract
{
    protected $_class;
    protected $_componentId;
    protected $_size;

    public function __construct($class, $componentId, $size)
    {
        $this->_class = $class;
        $this->_componentId = $componentId;
        $this->_size = $size;
    }

    public function load($row)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        $table = new $tablename(array('componentClass'=>$this->_class));
        $componentId = $this->_componentId . '-' . $row->id;
        $row = $table->find($componentId)->current();
        if ($row) {
            return $row->getFileUrl(null, $this->_size);
        } else {
            return '';
        }
    }
}
