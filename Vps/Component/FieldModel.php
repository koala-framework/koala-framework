<?php
class Vps_Component_FieldModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_data';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
}
