<?php
class Kwc_FormWizard_WizardFormAjax_Form2_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['useAjaxRequest'] = true;
        return $ret;
    }
}
