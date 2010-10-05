<?php
class Vps_Component_Cache_Fnf_MetaModelModel extends Vps_Component_Cache_Mysql_MetaModelModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'model', 'component_class', 'pattern', 'callback', 'meta_class'),
            'uniqueColumns' => array('model', 'component_class'),
            'default' => array('callback' => false)
        ));
        parent::__construct($config);
    }
}
