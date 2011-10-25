<?php
class Kwf_Component_FieldModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_data';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Kwf_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
}
