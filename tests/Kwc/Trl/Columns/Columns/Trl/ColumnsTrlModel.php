<?php
class Kwc_Trl_Columns_Columns_Trl_ColumnsTrlModel extends Kwc_Abstract_List_Trl_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('component_id', 'visible', 'data'),
            'primaryKey' => 'component_id',
            'siblingModels' => array(
                new Kwf_Model_Field(array('fieldName'=>'data'))
            )
        ));
        //rows get initialized in Kwc_Trl_Columns_Test::setUp
        parent::__construct($config);
    }
}
