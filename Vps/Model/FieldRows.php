<?php
class Vps_Model_FieldRows extends Vps_Model_Data_Abstract
    implements Vps_Model_RowsSubModel_Interface
{
    protected $_rowClass = 'Vps_Model_FieldRows_Row';
    protected $_rowsetClass = 'Vps_Model_FieldRows_Rowset';
    protected $_fieldName;
    protected $_data;

    public function __construct(array $config = array())
    {
        if (isset($config['fieldName'])) {
            $this->_fieldName = $config['fieldName'];
        }
        parent::__construct($config);
    }

    public function createRow(array $data=array())
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Field');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Field');
    }

    public function update($id, Vps_Model_FieldRows_Row $row, $rowData)
    {
        foreach ($this->_data[$row->getParentRow()->getInternalId()] as $k=>$i) {
            if (isset($i[$this->getPrimaryKey()]) && $i[$this->getPrimaryKey()] == $id) {
                $this->_data[$row->getParentRow()->getInternalId()][$k] = $rowData;
                $this->_updateParentRow($row->getParentRow());
                return $rowData[$this->getPrimaryKey()];
            }
        }
        throw new Vps_Exception("Can't find entry with id '$id'");
    }

    public function insert(Vps_Model_FieldRows_Row $row, $rowData)
    {
        $iId = $row->getParentRow()->getInternalId();
        if (!isset($rowData[$this->getPrimaryKey()])) {
            if (!isset($this->_autoId[$iId])) {
                $this->_autoId[$iId] = 0;
                foreach ($this->_data[$iId] as $k=>$i) {
                    if (isset($i[$this->getPrimaryKey()])) {
                        $this->_autoId[$iId] = max($i[$this->getPrimaryKey()], $this->_autoId[$iId]);
                    }
                }
            }
            $this->_autoId[$iId]++;
            $rowData[$this->getPrimaryKey()] = $this->_autoId[$iId];
        }
        $this->_data[$iId][] = $rowData;
        $this->_updateParentRow($row->getParentRow());
        return $rowData[$this->getPrimaryKey()];
    }

    public function delete($id, Vps_Model_FieldRows_Row $row)
    {
        foreach ($this->_data[$row->getParentRow()->getInternalId()] as $k=>$i) {
            if (isset($i[$this->getPrimaryKey()]) && $i[$this->getPrimaryKey()] == $id) {
                unset($this->_data[$row->getParentRow()->getInternalId()][$k]);
                $this->_updateParentRow($row->getParentRow());
                return;
            }
        }
        throw new Vps_Exception("Can't find entry with id '$id'");
    }

    private function _updateParentRow($parentRow)
    {
        $v = array(
            'data' => $this->_data[$parentRow->getInternalId()],
            'autoId' => $this->_autoId[$parentRow->getInternalId()]
        );
        $parentRow->{$this->_fieldName} = serialize($v);
    }

    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        $this->_data[$parentRow->getInternalId()] = array();

        $v = $parentRow->{$this->_fieldName};
        if (substr($v, 0, 13) == 'vpsSerialized') {
            $v = substr($v, 13);
        }
        $v = unserialize($v);
        if ($v) {
            $this->_autoId[$parentRow->getInternalId()] = $v['autoId'];
            foreach ($v['data'] as $i) {
                $this->_data[$parentRow->getInternalId()][] = $i;
            }
        } else {
            $this->_autoId[$parentRow->getInternalId()] = 0;
        }

        if (!is_object($select)) {
            $select = $this->select($select);
        }
        return new $this->_rowsetClass(array(
            'model' => $this,
            'data' => $this->_selectData($select, $this->_data[$parentRow->getInternalId()]),
            'parentRow' => $parentRow,
            'rowClass' => $this->_rowClass
        ));
    }

    public function createRowByParentRow(Vps_Model_Row_Interface $parentRow, array $data = array())
    {
        return $this->_createRow($data, array('parentRow' => $parentRow));
    }
}
