<?php
class Kwf_Component_Generator_Plugin_StatusUpdate_LogModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_statusupdate_log';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Kwf_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
}
