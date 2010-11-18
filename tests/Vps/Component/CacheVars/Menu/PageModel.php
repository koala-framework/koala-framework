<?php
class Vps_Component_CacheVars_Menu_PageModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false)
    );
}
