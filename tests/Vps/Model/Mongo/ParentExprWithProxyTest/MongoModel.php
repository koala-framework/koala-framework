<?php
class Vps_Model_Mongo_ParentExprWithProxyTest_MongoModel extends Vps_Model_Proxy
{
    protected $_referenceMap = array(
        'Parent' => array(
            'refModelClass' => 'Vps_Model_Mongo_ParentExprWithProxyTest_ParentModel',
            'column' => 'parent_id'
        )
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
        $this->_exprs['parent_name'] = new Vps_Model_Select_Expr_Parent('Parent', 'name');
    }
}
