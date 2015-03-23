<?php
class Kwc_Trl_PagesPlusTable_TestTableComponent_ChildModel extends Kwf_Model_FnF
{
    protected $_toStringField = 'id';
    protected $_columns = array('id', 'component_id', 'visible');
    protected $_data = array(
        array('id'=>1, 'component_id' => '1', 'visible' => true),
        array('id'=>2, 'component_id' => '1', 'visible' => false),
        array('id'=>3, 'component_id' => '1', 'visible' => true),
    );
}
