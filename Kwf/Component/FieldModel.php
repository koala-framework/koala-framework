<?php
/**
* This model is a easy-to-use model if you work with components. You will
* probably not want to create a special model for every component you create.
*/
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
