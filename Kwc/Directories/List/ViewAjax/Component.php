<?php
class Kwc_Directories_List_ViewAjax_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['assets']['files'][] = 'kwf/Kwc/Directories/List/ViewAjax/Component.js';
        $ret['assets']['dep'][] = 'KwfAutoGrid'; //TODO: less dep
        $ret['assets']['dep'][] = 'KwfHistoryState';

        return $ret;
    }


    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $cfg = Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($this->getData()->componentClass);
        $ret['config'] = array(
            'controllerUrl' => $cfg->getControllerUrl('View'),
            'componentId' => $this->getData()->componentId,
            'directoryUrl' => $this->getData()->parent->getComponent()->getItemDirectory()->url
        );
        return $ret;
    }
}
