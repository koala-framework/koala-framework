<?php
class Vps_Model_DbWithConnection_SiblingRelationExpr_RelationModel extends Vps_Model_Proxy
{
    protected $_referenceMap = array(
        'Master' => array(
            'refModelClass' => 'Vps_Model_DbWithConnection_SiblingRelationExpr_TestModel',
            'column' => 'master_id',
        )
    );
    public function __construct()
    {
        $config = array();
        $config['proxyModel'] = 'Vps_Model_DbWithConnection_SiblingRelationExpr_RelationDbModel';
        $this->_siblingModels[] = new Vps_Model_DbWithConnection_SiblingRelationExpr_RelationSiblingModel();
        parent::__construct($config);
    }

    public function dropTable()
    {
        $this->getProxyModel()->dropTable();
        $this->_siblingModels[0]->dropTable();
    }
}
