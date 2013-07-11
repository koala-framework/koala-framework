<?php
class Kwf_Component_Cache_Fnf_IncludesModel extends Kwf_Component_Cache_Mysql_IncludesModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'columns' => array('id', 'component_id', 'page_id', 'target_id')
        ));
        parent::__construct($config);
    }
}
