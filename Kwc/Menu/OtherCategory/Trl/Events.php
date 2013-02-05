<?php
class Kwc_Menu_OtherCategory_Trl_Events extends Kwc_Menu_OtherCategory_Events
{
    protected function _initSettings()
    {
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $masterMenuComponentClass = Kwc_Abstract::getSetting($masterComponentClass, 'menuComponentClass');
        $trlMenuComponentClass = Kwc_Chained_Abstract_Component::getChainedComponentClass($masterMenuComponentClass, 'Trl');
        $this->_menuComponentClass = $trlMenuComponentClass;
    }
}
