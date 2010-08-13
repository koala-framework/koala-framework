<?php
class Vps_Component_Cache_Fnf_MetaRowModel extends Vps_Component_Cache_Mysql_MetaRowModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'model', 'column', 'value', 'component_id', 'callback'),
            'uniqueColumns' => array('model', 'column', 'value', 'component_id'),
            'default' => array('callback' => 0)
        ));
        parent::__construct($config);
    }
}
