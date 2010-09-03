<?php
class Vps_Util_Model_Amazon_ProductsToNodes extends Vps_Model_Abstract
    implements Vps_Model_RowsSubModel_Interface
{
    protected $_rowsetClass = 'Vps_Model_Rowset_ParentRow';
    protected $_rowClass = 'Vps_Model_Row_Data_Abstract';

    protected $_referenceMap = array(
        'Node' => array(
            'column' => 'node_id',
            'refModelClass' => 'Vps_Util_Model_Amazon_Nodes'
        )
    );

    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        if ($select->getParts()) {
            throw new Vps_Exception_NotYetImplemented('Custom select is not yet implemented');
        }
        if (!($parentRow instanceof Vps_Util_Model_Amazon_Products_Row)) {
            throw new Vps_Exception('Only possible with amazon product row');
        }
        $pId = $parentRow->getInternalId();
        $item = $parentRow->getItem();
        $this->_data[$pId] = array();
        foreach ($item->BrowseNodes as $n) {
            $this->_data[$pId][] = array(
                'node_id' => $n
            );
        }

        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => array_keys($this->_data[$pId]),
            'parentRow' => $parentRow
        ));
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('Not possible');
    }

    public function getRowByDataKey($key, $parentRow)
    {
        $pId = $parentRow->getInternalId();
        if (!isset($this->_rows[$pId][$key])) {
            $this->_rows[$pId][$key] = new $this->_rowClass(array(
                'data' => $this->_data[$pId][$key],
                'model' => $this
            ));
        }
        return $this->_rows[$pId][$key];
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    protected function _getOwnColumns()
    {
        return array('id', 'node_id');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function createRowByParentRow(Vps_Model_Row_Interface $parentRow, array $data = array())
    {
        throw new Vps_Exception("read only");
    }
}
