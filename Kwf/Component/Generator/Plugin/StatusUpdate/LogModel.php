<?php
class Vps_Component_Generator_Plugin_StatusUpdate_LogModel extends Vps_Model_Db
{
    protected $_table = 'vpc_statusupdate_log';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
}
