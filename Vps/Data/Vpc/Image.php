<?php
class Vps_Data_Vpc_Image extends Vps_Data_Abstract
{
    protected $_class;
    protected $_size;

    public function __construct($class, $size)
    {
        $this->_class = $class;
        $this->_size = $size;
    }

    public function load($row)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        $table = new $tablename(array('componentClass'=>$this->_class));
        $componentId = $row->component_id . '-' . $row->id;
        $row = $table->find($componentId)->current();
        if ($row) {
            return $row->getFileUrl(null, $this->_size);
        } else {
            return '';
        }
    }
}
