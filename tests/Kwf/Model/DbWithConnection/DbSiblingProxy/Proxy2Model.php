<?php
class Kwf_Model_DbWithConnection_DbSiblingProxy_Proxy2Model extends Kwf_Model_Proxy
{
    protected $_proxyModel = 'Kwf_Model_DbWithConnection_DbSiblingProxy_DbModel';
    public function dropTable()
    {
        $this->_proxyModel->dropTable();
    }
}
