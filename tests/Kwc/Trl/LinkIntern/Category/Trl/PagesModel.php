<?php
class Kwc_Trl_LinkIntern_Category_Trl_PagesModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config['columns'] = array();
        $config['primaryKey'] = 'component_id';
        $config['data'] = array(
            array('component_id'=>'root-en-cat1_1', 'visible'=>true, 'name'=>'Foo1en', 'filename' => 'foo1en'),
            array('component_id'=>'root-en-cat1_2', 'visible'=>false, 'name'=>'Foo2en', 'filename' => 'foo2en'),
            array('component_id'=>'root-en-cat1_3', 'visible'=>true, 'name'=>'Foo3en', 'filename' => 'foo3en'),
        );
        parent::__construct($config);
    }
}
