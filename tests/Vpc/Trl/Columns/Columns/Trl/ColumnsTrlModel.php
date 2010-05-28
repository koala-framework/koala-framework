<?php
class Vpc_Trl_Columns_Columns_Trl_ColumnsTrlModel extends Vpc_Abstract_List_Trl_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('component_id', 'visible', 'data'),
            'primaryKey' => 'component_id',
            'siblingModels' => array(
                new Vps_Model_Field(array('fieldName'=>'data'))
            )
        ));
        $config['proxyModel']
            ->createRow(array('component_id'=>'root-en_test-1', 'visible' => 1))
            ->save();
        $config['proxyModel']
            ->createRow(array('component_id'=>'root-en_test-2', 'visible' => 1))
            ->save();
        $config['proxyModel']
            ->createRow(array('component_id'=>'root-en_test-3', 'visible' => 1))
            ->save();
        parent::__construct($config);
    }
}
