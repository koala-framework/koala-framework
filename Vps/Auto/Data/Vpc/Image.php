<?php
class Vps_Auto_Data_Vpc_Image extends Vps_Auto_Data_Abstract
{
    protected $_class;
    protected $_pageId;
    protected $_componentKey;

    public function __construct($class, $pageId, $componentKey)
    {
        $this->_class = $class;
        $this->_pageId = $pageId;
        $this->_componentKey = $componentKey;
    }

    public function load($row)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        $table = new $tablename(array('componentClass'=>$this->_class));
        $componentKey = $this->_componentKey . '-' . $row->id;
        $row = $table->find($this->_pageId, $componentKey)->current();
        if ($row) {
            return '<img src="' . $row->getFileUrl(null, 'mini') . '" />';
        } else {
            return '';
        }
    }
}
