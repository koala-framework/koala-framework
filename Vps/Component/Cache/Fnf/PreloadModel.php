<?php
class Vps_Component_Cache_Fnf_PreloadModel extends Vps_Component_Cache_Mysql_PreloadModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'page_id', 'preload_component_id', 'preload_type'),
            'uniqueColumns' => array('page_id', 'preload_component_id', 'preload_type')
        ));
        parent::__construct($config);
    }
}
