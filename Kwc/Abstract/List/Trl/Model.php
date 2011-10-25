<?php
class Kwc_Abstract_List_Trl_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_composite_list_trl';

    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }
}
