<?php
class Kwc_Trl_LinkTag_LinkTag_Extern_Trl_TestModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'root-en_test2-child', 'target'=>'http://www.vivid-planet.com/en', 'own_target' => true),
            )
        );
        parent::__construct($config);
    }
}
