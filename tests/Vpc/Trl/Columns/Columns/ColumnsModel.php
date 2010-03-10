<?php
class Vpc_Trl_Columns_Columns_ColumnsModel extends Vpc_Columns_ColumnsModel
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Vpc_Trl_Columns_Columns_Model';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'component_id', 'width'),
                'primaryKey' => 'id',
                'data'=> array(
                    array('id' => 1, 'component_id'=>'root-master_test', 'width'=>'100'),
                    array('id' => 2, 'component_id'=>'root-master_test', 'width'=>'100'),
                    array('id' => 3, 'component_id'=>'root-master_test', 'width'=>'50'),
                )
            ));
        parent::__construct($config);
    }
}
