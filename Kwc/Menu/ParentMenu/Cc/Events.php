<?php
class Kwc_Menu_ParentMenu_Cc_Events extends Kwc_Menu_ParentMenu_Events
{
    protected function _initSettings()
    {
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $masterMenuComponentClass = Kwc_Abstract::getSetting($masterComponentClass, 'menuComponentClass');
        $trlMenuComponentClass = Kwc_Chained_Abstract_Component::getChainedComponentClass($masterMenuComponentClass, 'Cc');
        $this->_menuComponentClass = $trlMenuComponentClass;
    }
}
