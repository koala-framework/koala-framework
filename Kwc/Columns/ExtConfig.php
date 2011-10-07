<?php
class Kwc_Columns_ExtConfig extends Kwc_Abstract_List_ExtConfigListEditButton
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['controllerUrl'] = $this->getControllerUrl();
        return $ret;
    }
}