<?php
class Vps_Model_RowsSubModel_Proxy extends Vps_Model_Proxy
    implements Vps_Model_RowsSubModel_Interface
{
    public function createRowByParentRow(Vps_Model_Row_Interface $parentRow, array $data = array())
    {
        while ($parentRow instanceof Vps_Model_Proxy_Row) $parentRow = $parentRow->getProxiedRow();
        $proxyRow = $this->_proxyModel->createRowByParentRow($parentRow);
        $ret = new $this->_rowClass(array(
            'row' => $proxyRow,
            'model' => $this
        ));
        $this->_rows[$proxyRow->getInternalId()] = $ret;
        $data = array_merge($this->_default, $data);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        return $ret;
    }

    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        while ($parentRow instanceof Vps_Model_Proxy_Row) $parentRow = $parentRow->getProxiedRow();
        $proxyRowset = $this->_proxyModel->getRowsByParentRow($parentRow, $select);
        return new $this->_rowsetClass(array(
            'rowset' => $proxyRowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
}
