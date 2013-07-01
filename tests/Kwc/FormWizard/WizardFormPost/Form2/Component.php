<?php
class Kwc_FormWizard_WizardFormPost_Form2_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useAjaxRequest'] = false;
        return $ret;
    }
}
