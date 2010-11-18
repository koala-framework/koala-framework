<?php
class Vps_Model_DbWithConnection_DbSiblingProxy_ProxyModel extends Vps_Model_Proxy
{
    protected $_proxyModel = 'Vps_Model_DbWithConnection_DbSiblingProxy_Proxy2Model';
    protected $_siblingModels = array('Vps_Model_DbWithConnection_DbSiblingProxy_SiblingModel');
    public function dropTable()
    {
        $this->_proxyModel->dropTable();
        $this->_siblingModels[0]->dropTable();
    }
}
