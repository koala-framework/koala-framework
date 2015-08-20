<?php
class Kwc_Abstract_Cards_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_basic_cards';
    protected $_rowClass = 'Kwc_Abstract_Cards_Row';
    protected $_toStringField = 'component';
    protected function _init()
    {
        $this->_siblingModels = array(
            new Kwf_Model_Field(array(
                'fieldName' => 'data'
            ))
        );
        parent::_init();
    }
}
