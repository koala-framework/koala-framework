<?php
class Vps_Component_Cache_Fnf_Model extends Vps_Component_Cache_Mysql_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'component_id', 'page_id', 'component_class', 'type', 'value', 'expire', 'deleted', 'content'),
            'uniqueColumns' => array('component_id', 'type', 'value')
        ));
        parent::__construct($config);
    }
}
