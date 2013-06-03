<?php
class Kwf_Component_Cache_Visible_Root_PagesModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
              'parent_id'=>'root', 'component'=>'child', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Bar', 'filename' => 'bar',
              'parent_id'=>1, 'component'=>'child', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
    );
}
