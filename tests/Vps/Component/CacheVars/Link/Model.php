<?php
class Vps_Component_CacheVars_Link_Model extends Vpc_Basic_LinkTag_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id' => 'root_link', 'component' => 'intern')
            ),
            'columns' => array('component_id', 'component'),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
