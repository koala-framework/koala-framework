<?php
class Kwc_FormWizard_WizardFormAjax_Form2_Model extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'number');
        $config['primaryKey'] = 'id';
        parent::__construct($config);
    }
}
