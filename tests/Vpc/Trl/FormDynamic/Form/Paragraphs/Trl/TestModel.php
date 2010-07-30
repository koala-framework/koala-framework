<?php
class Vpc_Trl_FormDynamic_Form_Paragraphs_Trl_TestModel extends Vpc_Paragraphs_Trl_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('component_id', 'visible'),
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test1-paragraphs-1', 'visible' => 1),
                array('component_id'=>'root-en_test1-paragraphs-2', 'visible' => 1),
                array('component_id'=>'root-en_test1-paragraphs-3', 'visible' => 1),
                array('component_id'=>'root-en_test1-paragraphs-4', 'visible' => 1),
            )
        ));
        parent::__construct($config);
    }
}
