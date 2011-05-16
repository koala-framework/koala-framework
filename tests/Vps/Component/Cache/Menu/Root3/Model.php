<?php
class Vps_Component_Cache_Menu_Root3_Model extends Vpc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'f1', 'filename' => 'f1',
                  'parent_id'=>'root', 'component'=>'menu1', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'f2', 'filename' => 'f2',
                  'parent_id'=>'1', 'component'=>'menu1', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'f3', 'filename' => 'f3',
                  'parent_id'=>'2', 'component'=>'menu1', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),

            array('id'=>11, 'pos'=>2, 'visible'=>true, 'name'=>'g1', 'filename' => 'g1',
                  'parent_id'=>'root', 'component'=>'menu2', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>12, 'pos'=>1, 'visible'=>true, 'name'=>'g2', 'filename' => 'g2',
                  'parent_id'=>'11', 'component'=>'menu2', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>13, 'pos'=>1, 'visible'=>true, 'name'=>'g3', 'filename' => 'g3',
                  'parent_id'=>'12', 'component'=>'menu2', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),

            array('id'=>21, 'pos'=>3, 'visible'=>true, 'name'=>'h1', 'filename' => 'h1',
                  'parent_id'=>'root', 'component'=>'menu3', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>22, 'pos'=>1, 'visible'=>true, 'name'=>'h2', 'filename' => 'h2',
                  'parent_id'=>'21', 'component'=>'menu3', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>23, 'pos'=>1, 'visible'=>true, 'name'=>'h3', 'filename' => 'h3',
                  'parent_id'=>'22', 'component'=>'menu3', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
        ));
        $config = array('proxyModel' => new Vps_Model_FnF($config));
        parent::__construct($config);
    }

    protected function _setupFilters()
    {
    }
}
