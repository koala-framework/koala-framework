<?php
class Kwf_Component_Cache_Fnf_IncludesModel extends Kwf_Component_Cache_Mysql_IncludesModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'type', 'target_id', 'target_type')
        ));
        parent::__construct($config);
    }
}
