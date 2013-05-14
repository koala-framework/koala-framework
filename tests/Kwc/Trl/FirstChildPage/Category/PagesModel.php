<?php
class Kwc_Trl_FirstChildPage_Category_PagesModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config['data'] = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo1', 'filename' => 'foo1',
                'parent_id'=>'root-master-cat1', 'component'=>'firstChildPage', 'is_home'=>false, 'hide'=>false),

            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Foo2', 'filename' => 'foo2',
                'parent_id'=>'1', 'component'=>'none', 'is_home'=>false, 'hide'=>false),

            array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo3',
                'parent_id'=>'1', 'component'=>'none', 'is_home'=>false, 'hide'=>false),

            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Foo4', 'filename' => 'foo4',
                'parent_id'=>'root-master-cat1', 'component'=>'firstChildPage', 'is_home'=>false, 'hide'=>false),

            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Foo5', 'filename' => 'foo5',
                'parent_id'=>'root-master-cat1', 'component'=>'firstChildPage', 'is_home'=>false, 'hide'=>false),

            array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'Foo6', 'filename' => 'foo6',
                'parent_id'=>'5', 'component'=>'firstChildPage', 'is_home'=>false, 'hide'=>false),

            array('id'=>7, 'pos'=>1, 'visible'=>true, 'name'=>'Foo7', 'filename' => 'foo7',
                'parent_id'=>'6', 'component'=>'none', 'is_home'=>false, 'hide'=>false),
            );
        parent::__construct($config);
    }
}
