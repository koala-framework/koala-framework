<?php
class Vps_Component_CacheVars_Box_Model extends Vps_Model_FnF
{
    protected $_columns = array('component_id');
    protected $_data = array(
        array('component_id' => 'root-box'),
        array('component_id' => 'root_boxOverwritten-box'),
        array('component_id' => 'root-boxUnique'),
        array('component_id' => 'root_boxOverwritten-boxUnique')
    );
    protected $_primaryKey = 'component_id';
}
