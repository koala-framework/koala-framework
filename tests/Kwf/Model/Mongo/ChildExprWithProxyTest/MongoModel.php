<?php
class Kwf_Model_Mongo_ChildExprWithProxyTest_MongoModel extends Kwf_Model_Proxy
{
    protected $_dependentModels = array(
        'Child' => 'Kwf_Model_Mongo_ChildExprWithProxyTest_ChildModel'
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
        $this->_exprs['child_count'] = new Kwf_Model_Select_Expr_Child_Count('Child');
    }
}
