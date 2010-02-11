<?php
class Vps_Component_DependingOnRows_PagesModel extends Vpc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                //10 linkt zu 2
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                    'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'hide'=>false),
                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                    'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
                array('id'=>10, 'pos'=>10, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false),

                //20 linkt zu 20
                array('id'=>20, 'pos'=>30, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false),

                //30 linkt zu 31
                array('id'=>30, 'pos'=>30, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false),
                array('id'=>31, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                    'parent_id'=>30, 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            )
        ));
        parent::__construct($config);
    }
}
