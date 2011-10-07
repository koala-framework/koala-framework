<?php
class Kwf_Form_CardsRealModels_Model_LastnameModel extends Kwf_Model_Db
{
    protected $_table;

    public function __construct($config = array())
    {
        $this->_table = new Kwf_Form_CardsRealModels_Model_LastnameTable();
        parent::__construct($config);
    }

    protected $_referenceMap = array(
        'RefWrapper' => array(
            'column' => 'wrapper_id',
            'refModelClass' => 'Kwf_Form_CardsRealModels_Model_WrapperModel'
        )
    );
}
