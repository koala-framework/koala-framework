<?php
class Kwf_Model_DbWithConnection_DbSiblingProxy_ProxyModel extends Kwf_Model_Proxy
{
    protected $_proxyModel = 'Kwf_Model_DbWithConnection_DbSiblingProxy_Proxy2Model';
    protected $_siblingModels = array('Kwf_Model_DbWithConnection_DbSiblingProxy_SiblingModel');
    public function dropTable()
    {
        $this->_proxyModel->dropTable();
        $this->_siblingModels[0]->dropTable();
    }
}
