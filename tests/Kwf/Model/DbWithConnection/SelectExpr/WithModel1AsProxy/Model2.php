<?php
class Kwf_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Model2 extends Kwf_Model_Db
{
    protected $_referenceMap = array(
        'Model1' => array(
            'column' => 'model1_id',
            'refModelClass' => 'Kwf_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Proxy1'
        )
    );
    public function __construct($config = array())
    {
        $config['table'] = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SelectExpr_Model2')->getTableName();
        parent::__construct($config);
    }
}
