<?php
class Kwc_Abstract_Cards_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ));
        return $ret;
    }
}
