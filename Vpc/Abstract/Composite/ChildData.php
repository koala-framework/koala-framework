<?php
class Vpc_Abstract_Composite_ChildData extends Vps_Data_Abstract implements Vps_Data_Vpc_ListInterface
{
    protected $_parentData = null;

    public function __construct(Vps_Data_Interface $parentData)
    {
        $this->_parentData = $parentData;
    }

    public function load($row)
    {
        return $this->_parentData->load($row);
    }

    public function setSubComponent($key)
    {
        $key = $this->_parentData->getSubComponent().$key;
        $this->_parentData->setSubComponent($key);
    }

    public function getSubComponent()
    {
        return $this->_parentData->getSubComponent();
    }
}
