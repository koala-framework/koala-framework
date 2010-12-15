<?php
class Vps_Model_DbWithConnection_DbSiblingProxy_Proxy2Model extends Vps_Model_Proxy
{
    protected $_proxyModel = 'Vps_Model_DbWithConnection_DbSiblingProxy_DbModel';
    public function dropTable()
    {
        $this->_proxyModel->dropTable();
    }
}
