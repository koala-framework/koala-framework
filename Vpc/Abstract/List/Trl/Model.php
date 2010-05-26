<?php
class Vpc_Abstract_List_Trl_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_composite_list_trl';

    protected function _init()
    {
        $this->_siblingModels[] = new Vps_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }
}
