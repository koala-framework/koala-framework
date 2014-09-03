<?php
class Kwc_Columns_Basic_Columns_TestChildModel extends Kwc_Columns_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'pos', 'visible', 'data'),
            'data' => array(
                array('id' => 1, 'component_id' => '3000-1', 'pos' => 1, 'visible' => 1),
                array('id' => 2, 'component_id' => '3000-1', 'pos' => 2, 'visible' => 1),
                array('id' => 3, 'component_id' => '3000-2', 'pos' => 1, 'visible' => 1),
                array('id' => 4, 'component_id' => '3000-2', 'pos' => 2, 'visible' => 1),
                array('id' => 5, 'component_id' => '3000-3', 'pos' => 1, 'visible' => 1),
                array('id' => 6, 'component_id' => '3000-3', 'pos' => 2, 'visible' => 1),
                array('id' => 7, 'component_id' => '3000-3', 'pos' => 3, 'visible' => 1),
                array('id' => 8, 'component_id' => '3000-4', 'pos' => 1, 'visible' => 1),
                array('id' => 9, 'component_id' => '3000-4', 'pos' => 2, 'visible' => 1),
                array('id' => 10, 'component_id' => '3000-4', 'pos' => 3, 'visible' => 1)
            ),
            'siblingModels' => array(
                new Kwf_Model_Field(array('fieldName'=>'data'))
            )
        ));
        $this->_referenceMap['Component']['refModelClass'] = 'Kwc_Columns_Basic_Columns_TestModel';
        parent::__construct($config);
    }
}
