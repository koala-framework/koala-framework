<?php
class Kwc_FormCache_Form_FormModel extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['columns'] = array('fullname', 'email', 'phone', 'content');
        $config['namespace'] = 'favourites_model_session';
        $config['primaryKey'] = 'fullname';
        parent::__construct($config);
    }
}
