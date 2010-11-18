<?php
class Vpc_Columns_TestColumnsModel extends Vpc_Abstract_List_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Vpc_Columns_TestModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'component_id', 'pos', 'visible', 'data'),
            'primaryKey' => 'id',
            'siblingModels' => array(
                new Vps_Model_Field(array('fieldName'=>'data'))
            )
        ));
        $config['proxyModel']
            ->createRow(array('id' => 1, 'component_id'=>3000, 'pos' => 1, 'visible' => 1, 'width'=>'100'))
            ->save();
        $config['proxyModel']
            ->createRow(array('id' => 2, 'component_id'=>3000, 'pos' => 1, 'visible' => 1, 'width'=>'100'))
            ->save();
        $config['proxyModel']
            ->createRow(array('id' => 3, 'component_id'=>3000, 'pos' => 1, 'visible' => 1, 'width'=>'50'))
            ->save();
        parent::__construct($config);
    }
}
