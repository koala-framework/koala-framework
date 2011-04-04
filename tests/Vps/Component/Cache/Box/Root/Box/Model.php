<?php
class Vps_Component_Cache_Box_Root_Box_Model extends Vps_Model_FnF
{
    protected $_columns = array('component_id', 'content');
    protected $_data = array(
        array('component_id' => 'root-box', 'content' => 'root-box'),
        array('component_id' => 'root-boxUnique', 'content' => 'root-boxUnique'),
        array('component_id' => 'root_child-box', 'content' => 'root_child-box')
    );
    protected $_primaryKey = 'component_id';
}
