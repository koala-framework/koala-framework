<?php
class Kwc_FormWizard_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form',
            'component' => 'Kwc_FormWizard_WizardFormPost_Component'
        );
        $ret['generators']['form2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form2',
            'component' => 'Kwc_FormWizard_WizardFormAjax_Component'
        );
        return $ret;
    }
}