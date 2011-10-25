<?php
class Kwf_Model_Mongo_ParentExprWithProxyTest_MongoModel extends Kwf_Model_Proxy
{
    protected $_referenceMap = array(
        'Parent' => array(
            'refModelClass' => 'Kwf_Model_Mongo_ParentExprWithProxyTest_ParentModel',
            'column' => 'parent_id'
        )
    );

    public function __construct()
    {
        $config = array(
            'proxyModel' => new Kwf_Model_Mongo_TestModel()
        );
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['parent_name'] = new Kwf_Model_Select_Expr_Parent('Parent', 'name');
    }
}
