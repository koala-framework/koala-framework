<?php
class Kwc_Trl_List_List_TestModel extends Kwc_Abstract_List_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Trl_List_List_TestOwnModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('id', 'component_id', 'pos', 'visible', 'data'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id'=>1, 'component_id'=>'root-master_test', 'pos' => 1, 'visible' => 1),
                array('id'=>2, 'component_id'=>'root-master_test', 'pos' => 2, 'visible' => 1),
                array('id'=>3, 'component_id'=>'root-master_test', 'pos' => 3, 'visible' => 1),
            ),
            'siblingModels' => array(
                new Kwf_Model_Field(array('fieldName'=>'data'))
            )
        ));
        parent::__construct($config);
    }
}
