<?php
class Kwc_FormWizard_WizardFormPost_Form1_Model extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'text');
        $config['primaryKey'] = 'id';
        parent::__construct($config);
    }
}
