<?php
class Kwf_Acl_Resource_MenuDropdown extends Kwf_Acl_Resource_Abstract
{
    private $_collapseIfSingleChild = false;

    public function setCollapseIfSingleChild($v)
    {
        $this->_collapseIfSingleChild = $v;
    }

    public function getCollapseIfSingleChild()
    {
        return $this->_collapseIfSingleChild;
    }
}
