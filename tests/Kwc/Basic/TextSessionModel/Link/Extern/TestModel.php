<?php
class Kwc_Basic_TextSessionModel_Link_Extern_TestModel extends Kwc_Basic_LinkTag_Extern_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_Session(array(
            'namespace' => 'TextSessionModel_Link_Extern_TestModel',
            'primaryKey' => 'component_id',
            'columns' => array(),
        ));
        parent::__construct($config);
    }
}

