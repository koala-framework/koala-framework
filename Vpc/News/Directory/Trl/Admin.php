<?php
class Vpc_News_Directory_Trl_Admin extends Vpc_Directories_Item_Directory_Trl_Admin
{
    protected function _getContentClass()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return Vpc_Abstract::getChildComponentClass($detail, 'child', 'content');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['items']['idSeparator'] = '_';
        $ret['items']['idPostfix'] = '-content';
        return $ret;
    }

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
