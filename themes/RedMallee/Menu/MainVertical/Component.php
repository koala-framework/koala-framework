<?php
class RedMallee_Menu_MainVertical_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = array('main', 'top');
        $ret['cssClass'] = ' webListNone';

        $ret['assets']['dep'][] = 'KwfOnReadyJQuery';
        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Menu/MainVertical/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['config'] = array(
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl(),
            'componentId' => $this->getData()->componentId
        );
        return $ret;
    }
}
