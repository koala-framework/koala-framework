<?php
class Kwc_Trl_Columns_Columns_Model extends Kwc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Children'] = 'Kwc_Trl_Columns_Columns_ColumnsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_test', 'data'=>''),
                )
            ));
        parent::__construct($config);
    }
}
