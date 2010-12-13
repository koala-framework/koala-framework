<?php
class Vps_Component_Cache_Fnf_ProcessInputModel extends Vps_Component_Cache_Mysql_ProcessInputModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'page_id',
            'columns' => array('page_id', 'process_component_ids'),
            'uniqueColumns' => array('page_id')
        ));
        parent::__construct($config);
    }
}
