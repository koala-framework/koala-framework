<?php
class Vps_Component_CacheVars_Link_InternModel extends Vpc_Basic_LinkTag_Intern_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id' => 'root_link-link', 'target' => 'root')
            ),
            'columns' => array('component_id', 'target'),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
