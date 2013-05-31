<?php
class Kwf_Component_Cache_PageMove_Root_PagesModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'1', 'filename' => '1',
            'parent_id'=>'root', 'component'=>'c1', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'
        ),
        array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'2', 'filename' => '2',
            'parent_id'=>'1', 'component'=>'c2', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'
        ),
        array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'3', 'filename' => '3',
            'parent_id'=>'root', 'component'=>'c3', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'
        )
    );
}
