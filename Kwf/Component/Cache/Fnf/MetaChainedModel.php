<?php
class Kwf_Component_Cache_Fnf_MetaChainedModel extends Kwf_Component_Cache_Mysql_MetaChainedModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'source_component_class', 'target_component_class'),
            'uniqueColumns' => array('source_component_class', 'target_component_class')
        ));
        parent::__construct($config);
    }
}
