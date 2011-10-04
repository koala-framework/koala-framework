<?php
class Vps_Model_Mongo_ChildExprWithProxyTest_MongoModel extends Vps_Model_Proxy
{
    protected $_dependentModels = array(
        'Child' => 'Vps_Model_Mongo_ChildExprWithProxyTest_ChildModel'
    );

    public function __construct()
    {
        $config = array(
            'proxyModel' => new Vps_Model_Mongo_TestModel()
        );
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['child_count'] = new Vps_Model_Select_Expr_Child_Count('Child');
    }
}
