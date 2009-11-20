<?php
class Vpc_Columns_TestModel extends Vpc_Columns_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Columns'] = 'Vpc_Columns_TestColumnsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>3000, 'data'=>''),
                )
            ));
        parent::__construct($config);
    }
}
