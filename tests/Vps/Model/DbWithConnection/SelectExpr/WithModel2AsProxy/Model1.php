<?php
class Vps_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Model1 extends Vps_Model_Db
{
    protected $_dependentModels = array(
        'Model2' => 'Vps_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Proxy2'
    );
    public function __construct($config = array())
    {
        $config['table'] = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1')->getTableName();
        $config['exprs'] = array(
            'count_model2' => new Vps_Model_Select_Expr_Child_Count('Model2')
        );
        parent::__construct($config);
    }
}
