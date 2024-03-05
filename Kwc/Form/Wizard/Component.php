<?php
class Kwc_Form_Wizard_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        //$ret['generators']['child']['component']['form1'] = 'Form2_Component';
        //$ret['generators']['child']['component']['form2'] = 'Form1_Component';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['currentForm'] = $ret[$ret['keys'][0]];
        return $ret;
    }
}
