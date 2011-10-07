<?php
class Kwc_Columns_TestModel extends Kwc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Children'] = 'Kwc_Columns_TestColumnsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'3000', 'data'=>''),
                )
            ));
        parent::__construct($config);
    }
}
