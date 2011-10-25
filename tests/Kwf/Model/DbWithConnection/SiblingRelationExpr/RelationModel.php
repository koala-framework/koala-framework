<?php
class Kwf_Model_DbWithConnection_SiblingRelationExpr_RelationModel extends Kwf_Model_Proxy
{
    protected $_referenceMap = array(
        'Master' => array(
            'refModelClass' => 'Kwf_Model_DbWithConnection_SiblingRelationExpr_TestModel',
            'column' => 'master_id',
        )
    );
    public function __construct()
    {
        $config = array();
        $config['proxyModel'] = 'Kwf_Model_DbWithConnection_SiblingRelationExpr_RelationDbModel';
        $this->_siblingModels[] = new Kwf_Model_DbWithConnection_SiblingRelationExpr_RelationSiblingModel();
        parent::__construct($config);
    }

    public function dropTable()
    {
        $this->getProxyModel()->dropTable();
        $this->_siblingModels[0]->dropTable();
    }
}
