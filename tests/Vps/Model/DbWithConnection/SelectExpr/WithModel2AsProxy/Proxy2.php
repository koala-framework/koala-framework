<?php
class Vps_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Proxy2 extends Vps_Model_Proxy
{
    private $_tableName;
    protected $_referenceMap = array(
        'Model1' => array(
            'column' => 'model1_id',
            'refModelClass' => 'Vps_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Model1'
        )
    );
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_Db(array(
            //nicht direkt Model2 verwenden, stattdessen nur den tabellennamen
            'table' => Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model2')->getTableName()
        ));
        parent::__construct($config);
    }

}
