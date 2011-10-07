<?php
class Kwf_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Proxy2 extends Kwf_Model_Proxy
{
    private $_tableName;
    protected $_referenceMap = array(
        'Model1' => array(
            'column' => 'model1_id',
            'refModelClass' => 'Kwf_Model_DbWithConnection_SelectExpr_WithModel2AsProxy_Model1'
        )
    );
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_Db(array(
            //nicht direkt Model2 verwenden, stattdessen nur den tabellennamen
            'table' => Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model2')->getTableName()
        ));
        parent::__construct($config);
    }

}
