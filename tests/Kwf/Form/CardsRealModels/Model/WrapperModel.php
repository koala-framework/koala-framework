<?php
class Kwf_Form_CardsRealModels_Model_WrapperModel extends Kwf_Model_Db
{
    protected $_table;
    protected $_siblingModels;
    protected $_rowClass = 'Kwf_Form_CardsRealModels_Model_WrapperModelRow';

    public function __construct($config = array())
    {
        $this->_siblingModels = array(
            'sibfirst' => 'Kwf_Form_CardsRealModels_Model_FirstnameModel',
            'siblast' => 'Kwf_Form_CardsRealModels_Model_LastnameModel'
        );

        $this->_table = new Kwf_Form_CardsRealModels_Model_WrapperTable();

        parent::__construct($config);
    }
}