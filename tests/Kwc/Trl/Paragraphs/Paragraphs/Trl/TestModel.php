<?php
class Vpc_Trl_Paragraphs_Paragraphs_Trl_TestModel extends Vpc_Paragraphs_Trl_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('component_id', 'visible'),
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test-1', 'visible' => 1),
                array('component_id'=>'root-en_test-2', 'visible' => 0),
                //root-de_test-3 kein eintrag = invisible
            )
        ));
        parent::__construct($config);
    }
}
