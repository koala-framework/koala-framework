<?php
class Kwc_Menu_Expanded_Trl_Events extends Kwc_Menu_Abstract_Events
{
    protected $_numLevels = 2;
    protected function _initSettings()
    {
        $mcc = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $this->_level = $menuLevel = Kwc_Abstract::getSetting($mcc, 'level');
    }
}
