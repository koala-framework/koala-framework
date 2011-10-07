<?php
class Kwf_Model_DbWithConnection_SiblingRelationExpr_TestModel extends Kwf_Model_Proxy
{
    protected $_dependentModels = array(
        'Relation' => 'Kwf_Model_DbWithConnection_SiblingRelationExpr_RelationModel'
    );
    public function __construct()
    {
        $config['proxyModel'] = 'Kwf_Model_DbWithConnection_SiblingRelationExpr_MasterDbModel';
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['sum_foo']
            = new Kwf_Model_Select_Expr_Child('Relation',
                    new Kwf_Model_Select_Expr_Sum('foo'));

        $this->_exprs['sum_fooplusone']
            = new Kwf_Model_Select_Expr_Child('Relation',
                    new Kwf_Model_Select_Expr_Sum(new Kwf_Model_Select_Expr_Sql('foo+1', array('foo'))));

    }

    public function dropTable()
    {
        $this->getProxyModel()->dropTable();
    }
}
