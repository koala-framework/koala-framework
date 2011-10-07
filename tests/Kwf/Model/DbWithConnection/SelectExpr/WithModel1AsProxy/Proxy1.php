<?php
class Vps_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Proxy1 extends Vps_Model_Proxy
{
    private $_tableName;
    protected $_dependentModels = array(
        'Model2' => 'Vps_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Model2'
    );
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_Db(array(
            //nicht direkt Model2 verwenden, stattdessen nur den tabellennamen
            'table' => Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1')->getTableName()
        ));
        $config['exprs'] = array(
            'count_model2' => new Vps_Model_Select_Expr_Child_Count('Model2')
        );
        parent::__construct($config);
    }

}
