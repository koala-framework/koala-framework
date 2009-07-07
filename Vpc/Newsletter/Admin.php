<?php
class Vpc_Newsletter_Admin extends Vpc_Directories_Item_Directory_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    protected function _getContentClass()
    {
        return Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['items']['idSeparator'] = '_';
        return $ret;
    }
}
