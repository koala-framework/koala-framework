<?php
class Vps_Component_Plugin_AccessByMail_Model extends Vps_Model_Db
{
    protected $_table = 'vpc_access_by_mail';

    protected function _init()
    {
        parent::_init();
        $this->_filters['key'] = new Vps_Filter_Row_Random();
        $this->_filters['date'] = new Vps_Filter_Row_CurrentDateTime();
    }
}
