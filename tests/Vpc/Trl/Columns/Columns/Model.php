<?php
class Vpc_Trl_Columns_Columns_Model extends Vpc_Columns_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Columns'] = 'Vpc_Trl_Columns_Columns_ColumnsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_test', 'data'=>''),
                )
            ));
        parent::__construct($config);
    }
}
