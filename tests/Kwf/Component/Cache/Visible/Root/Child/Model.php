<?php
class Kwf_Component_Cache_Visible_Root_Child_Model extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';

    protected $_data = array(
        array('component_id'=>'1_child', 'content'=>'foo'),
        array('component_id'=>'2_child', 'content'=>'foo'),
        array('component_id'=>'root-1_child', 'content'=>'foo')
    );
}
