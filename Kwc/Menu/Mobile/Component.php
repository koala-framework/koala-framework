<?php
class Kwc_Menu_Mobile_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = array('main');
        $ret['rootElementClass'] = 'kwfUp-webListNone kwfUp-webStandard kwfUp-webMenu kwfUp-default';
        $ret['placeholder']['menuLink'] = trlKwfStatic('Menu');
        $ret['showSubmenus'] = true;

        $ret['showSelectedPageInList'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['config'] = array(
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl(),
            'subrootComponentId' => $this->getData()->getSubroot()->componentId,
            'componentId' => $this->getData()->componentId,
            'showSubmenus' => $this->getSetting($this->getData()->componentClass, 'showSubmenus')
        );
        return $ret;
    }
}
