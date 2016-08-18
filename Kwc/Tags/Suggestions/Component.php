<?php
class Kwc_Tags_Suggestions_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assets']['dep'][] = 'KwfClearOnFocus';
        $ret['menuConfig'] = 'Kwc_Tags_Suggestions_MenuConfig';
        $ret['componentName'] = trlKwfStatic('New Tags');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['config'] = array(
            'componentId' => $this->getData()->componentId,
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl()
        );
        return $ret;
    }
}
