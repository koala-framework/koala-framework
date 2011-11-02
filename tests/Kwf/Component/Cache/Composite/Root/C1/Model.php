<?php
class Kwf_Component_Cache_Composite_Root_C1_Model extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'data'=>array(
                array('component_id' => 'root-c1', 'has_content' => true, 'content' => 'foo'),
            ),
            'primaryKey' => 'component_id'
        );
        parent::__construct($config);
    }
}
