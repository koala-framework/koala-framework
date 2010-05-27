<?php
/**
 * Kann im Web verwendet werden, wenn Einstellungen in der Settings-Form
 * übersetzt werden müssen
 */
class Vpc_List_Gallery_Trl_AdminTrlSettings extends Vpc_List_Gallery_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['tabs']['tabs']['settings']['controllerUrl'] = $this->getControllerUrl('Settings');
        $ret['tabs']['tabs']['images']['controllerUrl'] = $this->getControllerUrl();
        return $ret;
    }
}
