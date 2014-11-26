<?php
class Kwf_Model_FnF_ExprParentContains_Model extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'Parent' => 'parent_id->Kwf_Model_FnF_ExprParentContains_ParentModel'
    );
    protected $_data = array(
        array('id' => 1, 'parent_id' => 1, 'foo'=>'foo1'),
        array('id' => 2, 'parent_id' => 2, 'foo'=>'foo2'),
        array('id' => 3, 'parent_id' => 1, 'foo'=>'foo3'),
    );
}
