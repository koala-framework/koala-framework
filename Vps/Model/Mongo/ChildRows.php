<?php
class Vps_Model_Mongo_ChildRows extends Vps_Model_Data_Abstract
    implements Vps_Model_RowsSubModel_Interface
{
    protected $_rowClass = 'Vps_Model_Mongo_ChildRows_Row';
    protected $_rowsetClass = 'Vps_Model_Mongo_ChildRows_Rowset';
    protected $_primaryKey = 'intern_id';
    protected $_fieldName;

    public function __construct(array $config = array())
    {
        if (isset($config['fieldName'])) {
            $this->_fieldName = $config['fieldName'];
        }
        parent::__construct($config);
    }

    public function createRow(array $data=array())
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Mongo_ChildRows');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Mongo_ChildRows');
    }

    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        $this->_data[$parentRow->getInternalId()] = array();

        $v = $parentRow->{$this->_fieldName};
        if ($v) {
            if (!is_array($v)) throw new Vps_Exception("value is not an array but a ".gettype($v));
            //TODO: _data nicht duplizieren, sondern in getRowByDataKey erst machen (performance)
            $i = 0;
            foreach (array_keys($v) as $k) {
                $v[$k]['intern_id'] = ++$i; //TODO ist das eindeutig genug (ist nur eindeutig PRO parentRow)
                                            // aber nachdem man hierher eh nur per getRowsByParentRow kommt müsste es passen
            }
            $this->_data[$parentRow->getInternalId()] = $v;
        } else {
            $this->_data[$parentRow->getInternalId()] = array();
        }

        if (!is_object($select)) {
            $select = $this->select($select);
        }
        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $this->_selectDataKeys($select, $this->_data[$parentRow->getInternalId()]),
            'parentRow' => $parentRow
        ));
    }

    public function createRowByParentRow(Vps_Model_Row_Interface $parentRow, array $data = array())
    {
        return $this->_createRow($data, array('parentRow' => $parentRow));
    }

    public function getRowByDataKey($key, $parentRow)
    {
        if (!isset($this->_rows[$parentRow->getInternalId()][$key])) {
            $this->_rows[$parentRow->getInternalId()][$key] = new $this->_rowClass(array(
                'data' => $this->_data[$parentRow->getInternalId()][$key],
                'model' => $this,
                'parentRow' => $parentRow
            ));
        }
        return $this->_rows[$parentRow->getInternalId()][$key];
    }

    private function _updateParentRow($parentRow)
    {
        $v = $this->_data[$parentRow->getInternalId()];
        foreach ($v as $k=>$i) {
            unset($v[$k]['intern_id']);
        }
        $parentRow->{$this->_fieldName} = $v;
    }

    public function update(Vps_Model_Mongo_ChildRows_Row $row, $rowData)
    {
        $iId = $row->getModelParentRow()->getInternalId();
        foreach ($this->_rows[$iId] as $k=>$i) {
            if ($row === $i) {
                $this->_data[$iId][$k] = $rowData;
                $this->_updateParentRow($row->getModelParentRow());
                //return $rowData[$this->getPrimaryKey()];
                return;
            }
        }
        throw new Vps_Exception("Can't find entry");
    }

    public function insert(Vps_Model_Mongo_ChildRows_Row $row, $rowData)
    {
        $iId = $row->getModelParentRow()->getInternalId();
        if (!isset($this->_data[$iId])) {
            //setzt _data (TODO: effizienter machen?)
            $this->getRowsByParentRow($row->getModelParentRow());
        }
        $this->_data[$iId][] = $rowData;
        $this->_rows[$iId][count($this->_data[$iId])-1] = $row;
        $this->_updateParentRow($row->getModelParentRow());
        //return $rowData[$this->getPrimaryKey()];
    }

    public function delete(Vps_Model_Mongo_ChildRows_Row $row)
    {
        foreach ($this->_rows[$row->getModelParentRow()->getInternalId()] as $k=>$i) {
            if ($row === $i) {
                unset($this->_data[$row->getModelParentRow()->getInternalId()][$k]);
                unset($this->_rows[$row->getModelParentRow()->getInternalId()][$k]);
                $this->_updateParentRow($row->getModelParentRow());
                return;
            }
        }
        throw new Vps_Exception("Can't find entry");
    }


    public function getUniqueIdentifier() {
        throw new Vps_Exception("no unique identifier set");
    }
}
