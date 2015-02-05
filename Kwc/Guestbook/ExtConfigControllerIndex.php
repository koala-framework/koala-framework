<?php
class Kwc_Guestbook_ExtConfigControllerIndex extends Kwc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['items']['controllerUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Comments');
        return $ret;
    }
}
