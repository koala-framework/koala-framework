<?php
class Kwc_Abstract_Cards_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        $ret['apiContent'] = 'Kwc_Abstract_Cards_Trl_ApiContent';
        $ret['apiContentType'] = 'cards';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['child'] = $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ));
        return $ret;
    }
}
