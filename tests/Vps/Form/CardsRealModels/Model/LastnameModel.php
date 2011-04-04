<?php
class Vps_Form_CardsRealModels_Model_LastnameModel extends Vps_Model_Db
{
    protected $_table;

    public function __construct($config = array())
    {
        $this->_table = new Vps_Form_CardsRealModels_Model_LastnameTable();
        parent::__construct($config);
    }

    protected $_referenceMap = array(
        'RefWrapper' => array(
            'column' => 'wrapper_id',
            'refModelClass' => 'Vps_Form_CardsRealModels_Model_WrapperModel'
        )
    );
}
