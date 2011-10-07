<?php
class Kwc_Trl_Paragraphs_Paragraphs_TestModel extends Kwc_Paragraphs_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('id', 'component_id', 'pos', 'visible', 'component'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id' => 1, 'component_id'=>'root-master_test', 'pos'=>1, 'visible' => 1, 'component' => 'child'),
                array('id' => 2, 'component_id'=>'root-master_test', 'pos'=>1, 'visible' => 1, 'component' => 'child'),
                array('id' => 3, 'component_id'=>'root-master_test', 'pos'=>1, 'visible' => 1, 'component' => 'child'),
            )
        ));
        parent::__construct($config);
    }
}
