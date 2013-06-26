<?php
class Kwc_FormWizard_WizardFormAjax_Form2_FrontendForm extends Kwf_Form
{
    protected $_model = 'Kwc_FormWizard_WizardFormAjax_Form2_Model';

    protected function _init()
    {
        $this->add(new Kwf_Form_Field_TextField('number', trlStatic('number')));
        parent::_init();
    }
}
