<?php
class Vps_Component_Cache_Chained_Master_Model extends Vps_Model_Fnf
{
    protected $_columns = array('component_id', 'value');
    protected $_data = array(
        array('component_id' => 'root-master', 'value' => 'foo')
    );
    protected $_primaryKey = 'component_id';
}
