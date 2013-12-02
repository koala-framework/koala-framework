<?php
class Kwc_Menu_Mobile_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = array('main');
        $ret['cssClass'] = 'webListNone';

        $ret['assets']['dep'][] = 'mustache';
        $ret['assets']['dep'][] = 'KwfOnReadyJQuery';
        $ret['assets']['files'][] = 'kwf/Kwc/Menu/Mobile/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['config'] = array(
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl(),
            'subrootComponentId' => $this->getData()->getSubroot()->componentId,
            'componentId' => $this->getData()->componentId
        );
        return $ret;
    }
}