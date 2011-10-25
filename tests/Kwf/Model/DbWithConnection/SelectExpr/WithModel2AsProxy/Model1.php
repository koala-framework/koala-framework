<?php
class Kwf_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Model1 extends Kwf_Model_Db
{
    protected $_dependentModels = array(
        'Model2' => 'Kwf_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Proxy2'
    );
    public function __construct($config = array())
    {
        $config['table'] = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model1')->getTableName();
        $config['exprs'] = array(
            'count_model2' => new Kwf_Model_Select_Expr_Child_Count('Model2')
        );
        parent::__construct($config);
    }
}
