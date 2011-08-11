<?php
class Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Model extends Vps_Model_FnF
{
    protected $_columns = array('component_id', 'content');
    protected $_data = array(
        array('component_id' => 'root-ic-child', 'content' => 'root-ic-child'),
        array('component_id' => '1-ic-child', 'content' => '1-ic-child')
    );
    protected $_primaryKey = 'component_id';
}
