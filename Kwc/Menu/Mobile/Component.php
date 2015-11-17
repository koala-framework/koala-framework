<?php
class Kwc_Menu_Mobile_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = array('main');
        $ret['rootElementClass'] = 'kwfUp-webListNone kwfUp-webStandard kwfUp-webMenu kwfUp-default';
        $ret['placeholder']['menuLink'] = trlKwfStatic('Menu');

        $ret['showSelectedPageInList'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['config'] = array(
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl(),
            'subrootComponentId' => $this->getData()->getSubroot()->componentId,
            'componentId' => $this->getData()->componentId
        );
        return $ret;
    }
}
