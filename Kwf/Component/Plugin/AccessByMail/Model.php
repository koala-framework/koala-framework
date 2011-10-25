<?php
class Kwf_Component_Plugin_AccessByMail_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_access_by_mail';

    protected function _init()
    {
        parent::_init();
        $this->_filters['key'] = new Kwf_Filter_Row_Random();
        $this->_filters['date'] = new Kwf_Filter_Row_CurrentDateTime();
    }
}
