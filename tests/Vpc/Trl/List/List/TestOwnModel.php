<?php
class Vpc_Trl_List_List_TestOwnModel extends Vpc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Children'] = 'Vpc_Trl_List_List_TestModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_test', 'data'=>''),
                )
            ));
        parent::__construct($config);
    }
}
