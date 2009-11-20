<?php
class Vpc_Columns_TestColumnsModel extends Vpc_Columns_ColumnsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Vpc_Columns_TestModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'component_id', 'width'),
                'primaryKey' => 'id',
                'data'=> array(
                    array('id' => 1, 'component_id'=>3000, 'width'=>'100'),
                    array('id' => 2, 'component_id'=>3000, 'width'=>'100'),
                    array('id' => 3, 'component_id'=>3000, 'width'=>'50'),
                )
            ));
        parent::__construct($config);
    }
}
