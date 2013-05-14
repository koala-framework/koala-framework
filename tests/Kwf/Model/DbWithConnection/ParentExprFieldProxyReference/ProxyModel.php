<?php
class Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_ProxyModel extends Kwf_Model_Proxy
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = 'Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_ChildModel';
        $this->_siblingModels['sibling'] = 'Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_SiblingModel';
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_ParentModel'
        );
        $this->_exprs['foo'] = new Kwf_Model_Select_Expr_Parent('Parent', 'foo');
    }
}
