<?php
class Vps_Component_PagesController_PagesGeneratorActions_PagesModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'hide'=>false),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test2',
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>4, 'pos'=>3, 'visible'=>true, 'name'=>'Special', 'filename' => 'special',
                  'parent_id'=>'root', 'component'=>'special', 'is_home'=>false, 'hide'=>false),
    );
}
