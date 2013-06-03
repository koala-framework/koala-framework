<?php
class Kwf_Component_Cache_ProcessInput_PageTestModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'bar', 'filename' => 'bar',
                  'parent_id'=>'root', 'component'=>'withProcessInput', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'baz', 'filename' => 'baz',
                  'parent_id'=>'root', 'component'=>'containsWithProcessInput', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>4, 'pos'=>4, 'visible'=>true, 'name'=>'par1', 'filename' => 'par1',
                  'parent_id'=>'root', 'component'=>'paragraphs', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>5, 'pos'=>5, 'visible'=>true, 'name'=>'par2', 'filename' => 'par2',
                  'parent_id'=>'root', 'component'=>'paragraphs', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
    );
}
