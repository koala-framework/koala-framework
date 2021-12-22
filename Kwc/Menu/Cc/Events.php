<?php
class Kwc_Menu_Cc_Events extends Kwc_Menu_Events
{
    protected function _initSettings()
    {
        $mcc = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $this->_level = $menuLevel = Kwc_Abstract::getSetting($mcc, 'level');
        $this->_emptyIfSingleEntry = $menuLevel = Kwc_Abstract::getSetting($mcc, 'emptyIfSingleEntry');
    }
}
