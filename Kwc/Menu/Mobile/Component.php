<?php
class Kwc_Menu_Mobile_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = array('main');
        $ret['cssClass'] = 'webListNone webStandard webMenu default';
        $ret['placeholder']['menuLink'] = trlKwfStatic('Menu');

        $ret['showSelectedPageInList'] = true;
        $ret['assetsDefer']['dep'][] = 'jQuery';
        $ret['assetsDefer']['dep'][] = 'underscore';
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
