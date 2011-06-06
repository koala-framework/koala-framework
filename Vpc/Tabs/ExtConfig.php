<?php
class Vpc_Tabs_ExtConfig extends Vpc_Abstract_List_ExtConfigListEditButton
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['controllerUrl'] = $this->getControllerUrl();
        return $ret;
    }
}