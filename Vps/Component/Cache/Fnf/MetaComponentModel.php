<?php
class Vps_Component_Cache_Fnf_MetaComponentModel extends Vps_Component_Cache_Mysql_MetaComponentModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'component_id', 'component_class', 'target_component_id', 'target_component_class'),
            'uniqueColumns' => array('component_id', 'target_component_id')
        ));
        parent::__construct($config);
    }
}
