<?php
class Vps_Component_Cache_Fnf_MetaCallbackModel extends Vps_Component_Cache_Mysql_MetaCallbackModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'model', 'field', 'value', 'component_id'),
            'uniqueColumns' => array('model', 'field', 'value')
        ));
        parent::__construct($config);
    }
}
