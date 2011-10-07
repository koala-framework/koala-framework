<?php
class Vps_Component_Cache_UrlClearCache_PageTestModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'bar', 'filename' => 'bar',
                  'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
    );
}
