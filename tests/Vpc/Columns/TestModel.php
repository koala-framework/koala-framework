<?php
class Vpc_Columns_TestModel extends Vpc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Children'] = 'Vpc_Columns_TestColumnsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'3000', 'data'=>''),
                )
            ));
        parent::__construct($config);
    }
}
