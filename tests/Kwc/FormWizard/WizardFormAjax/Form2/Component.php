<?php
class Kwc_FormWizard_WizardFormAjax_Form2_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useAjaxRequest'] = true;
        return $ret;
    }
}
