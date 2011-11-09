<?php
class Kwf_Component_Cache_Box_IcRoot_InheritContent_Child_Model extends Kwf_Model_FnF
{
    protected $_columns = array('component_id', 'content', 'has_content');
    protected $_data = array(
        array('component_id' => 'root-ic-child', 'content' => 'root-ic-child', 'has_content' => true),
        array('component_id' => '1-ic-child', 'content' => '1-ic-child', 'has_content' => true),
        array('component_id' => '2-ic-child', 'content' => '2-ic-child', 'has_content' => false)
    );
    protected $_primaryKey = 'component_id';
}
