<?php
class Vpc_News_Directory_Trl_Admin extends Vpc_Directories_Item_Directory_Trl_Admin
{
    protected function _getPluginParentComponents()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return array($detail, $this->_class);
    }

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    protected function _addResourcesBySameClassResourceName($c)
    {
        $ret = parent::_addResourcesBySameClassResourceName($c);
        $ret .= ' '.$c->getLanguageData()->name;
        return $ret;
    }
}
