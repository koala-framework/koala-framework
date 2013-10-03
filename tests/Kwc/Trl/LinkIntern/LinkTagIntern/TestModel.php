<?php
class Kwc_Trl_LinkIntern_LinkTagIntern_TestModel extends Kwc_Basic_LinkTag_Intern_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'root-master_test1', 'target'=>'1'),
                array('component_id'=>'root-master_test2', 'target'=>'2'),
                array('component_id'=>'root-master_test3', 'target'=>'3'),
            )
        ));
        parent::__construct($config);
    }
}
