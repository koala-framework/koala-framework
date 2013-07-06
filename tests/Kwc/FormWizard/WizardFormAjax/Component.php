<?php
class Kwc_FormWizard_WizardFormAjax_Component extends Kwc_Form_Wizard_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form1'] = 'Kwc_FormWizard_WizardFormAjax_Form1_Component';
        $ret['generators']['child']['component']['form2'] = 'Kwc_FormWizard_WizardFormAjax_Form2_Component';
        return $ret;
    }
}
