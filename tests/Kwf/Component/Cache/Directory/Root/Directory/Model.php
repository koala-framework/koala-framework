<?php
class Vps_Component_Cache_Directory_Root_Directory_Model extends Vps_Model_FnF
{
    protected $_columns = array('id', 'component_id', 'content');
    protected $_data = array(
        //array('id' => 1, 'component_id' => 'root_dir', 'content' => 'd1'),
        //array('id' => 2, 'component_id' => 'root_dir', 'content' => 'd2')
    );
    protected $_primaryKey = 'id';
}
