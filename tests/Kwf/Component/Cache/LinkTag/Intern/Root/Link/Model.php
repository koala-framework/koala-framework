<?php
class Kwf_Component_Cache_LinkTag_Intern_Root_Link_Model extends Kwc_Basic_LinkTag_Intern_Model
{
    public function __construct()
    {
        $config = array(
            'data'=>array(
                array('component_id' => 'root_link', 'target' => 1, 'rel' => null, 'param' => null),
            ),
            'columns' => array(),
            'primaryKey' => 'component_id'
        );
        parent::__construct(array('proxyModel' => new Kwf_Model_FnF($config)));
    }
}
