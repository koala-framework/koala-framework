<?php
class Kwc_Trl_FirstChildPage_Category_Trl_PagesModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config['columns'] = array();
        $config['primaryKey'] = 'component_id';
        $config['data'] = array(
            array('component_id'=>'root-en-cat1_1', 'visible'=>true, 'name'=>'Foo1en', 'filename' => 'foo1en'),
            array('component_id'=>'root-en-cat1_2', 'visible'=>true, 'name'=>'Foo2en', 'filename' => 'foo2en'),
            array('component_id'=>'root-en-cat1_3', 'visible'=>true, 'name'=>'Foo3en', 'filename' => 'foo3en'),
            array('component_id'=>'root-en-cat1_4', 'visible'=>true, 'name'=>'Foo4en', 'filename' => 'foo4en'),
            array('component_id'=>'root-en-cat1_5', 'visible'=>true, 'name'=>'Foo5en', 'filename' => 'foo5en'),
            array('component_id'=>'root-en-cat1_6', 'visible'=>true, 'name'=>'Foo6en', 'filename' => 'foo6en'),
            array('component_id'=>'root-en-cat1_7', 'visible'=>true, 'name'=>'Foo7en', 'filename' => 'foo7en'),
        );
        parent::__construct($config);
    }
}
