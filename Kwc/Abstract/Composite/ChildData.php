<?php
class Kwc_Abstract_Composite_ChildData extends Kwf_Data_Abstract implements Kwf_Data_Kwc_ListInterface
{
    protected $_parentData = null;

    public function __construct(Kwf_Data_Interface $parentData)
    {
        $this->_parentData = $parentData;
    }

    public function load($row, array $info = array())
    {
        $ret = $this->_parentData->load($row, $info);
        return $ret;
    }

    public function setSubComponent($key)
    {
        $key = $key.$this->_parentData->getSubComponent();
        $this->_parentData->setSubComponent($key);
    }

    public function getSubComponent()
    {
        return $this->_parentData->getSubComponent();
    }
}
