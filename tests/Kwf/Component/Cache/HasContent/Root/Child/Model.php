<?php
class Vps_Component_Cache_HasContent_Root_Child_Model extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'data'=>array(
                array('component_id' => 'root_child', 'has_content' => false),
            ),
            'primaryKey' => 'component_id'
        );
        parent::__construct($config);
    }
}
