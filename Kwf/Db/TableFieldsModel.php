<?php
class Vps_Db_TableFieldsModel extends Vps_Model_Data_Abstract
    implements Vps_Model_RowsSubModel_Interface
{
    protected $_dependentModels = array(
        'Fields' => 'Vps_Db_TableFieldsModel'
    );
    private $_db;
    protected $_primaryKey = 'field';
    protected $_columns = array('field', 'type', 'null', 'key', 'default', 'extra');
    protected $_rowClass = 'Vps_Db_TableFieldsModel_Row';
    protected $_rowsetClass = 'Vps_Db_TableFieldsModel_Rowset';

    public function __construct(array $options = array())
    {
        if (isset($options['db'])) $this->_db = $options['db'];
        parent::__construct($options);
    }

    protected function _getDb()
    {
        if (isset($this->_db)) return $this->_db;
        return Vps_Registry::get('db');
    }

    public function createRow(array $data=array())
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Field');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Field');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception("no unique identifier set");
    }


    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        if (!isset($this->_data[$parentRow->getInternalId()])) {
            $this->_data[$parentRow->getInternalId()] = array();
            $fields = $parentRow->getModel()->getDb()
                ->fetchAssoc("SHOW FIELDS FROM {$parentRow->table}");
            foreach ($fields as $i) {
                $this->_data[$parentRow->getInternalId()][] = array(
                    'field' => $i['Field'],
                    'type' => $i['Type'],
                    'null' => ($i['Null'] == 'YES' ? 1 : 0),
                    'key' => $i['Key'],
                    'default' => $i['Default'],
                    'extra' => $i['Extra'],
                );
            }
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

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        $iId = $row->getSubModelParentRow()->getInternalId();
        foreach ($this->_rows[$iId] as $k=>$i) {
            if ($row === $i) {
                $this->_alterTableField($row, $this->_data[$iId][$k]['field']);
                $this->_data[$iId][$k] = $rowData;
                return $rowData[$this->getPrimaryKey()];
            }
        }
        throw new Vps_Exception("Can't find entry");
    }

    private function _alterTableField($row, $changeName = null)
    {
        if (!$row->field) {
            throw new Vps_ClientException("field is required");
        }
        if (!$row->type) {
            throw new Vps_ClientException("type is required");
        }
        if (!$row->null && is_null($row->default) && $row->extra != 'auto_increment') {
            throw new Vps_ClientException("invalid default value, null is not allowed");
        }
        $iId = $row->getSubModelParentRow()->getInternalId();
        $sql = "ALTER TABLE ";
        $sql .= $row->getSubModelParentRow()->table." ";
        if ($changeName) {
            $sql .= "CHANGE $changeName ";
        } else {
            $sql .= "ADD ";
        }
        $sql .= "{$row->field} {$row->type} ";
        $sql .= $row->null ? 'NULL ' : 'NOT NULL ';

        if (is_null($row->default)) {
            $sql .= "DEFAULT NULL ";
        } else if ($row->default) {
            $sql .= "DEFAULT '{$row->default}' ";
        }
        $sql .= $row->extra;
        $row->getSubModelParentRow()->getModel()->getDb()->query(trim($sql));
    }

    public function insert(Vps_Model_Row_Interface $row, $rowData)
    {
        $iId = $row->getSubModelParentRow()->getInternalId();
        $this->_alterTableField($row);
        $this->_data[$iId][] = $rowData;
        $this->_rows[$iId][count($this->_data[$iId])-1] = $row;
        return $rowData[$this->getPrimaryKey()];
    }

    public function delete(Vps_Model_Row_Interface $row)
    {
        $iId = $row->getSubModelParentRow()->getInternalId();

        if (!$row->field) {
            throw new Vps_ClientException("field is required");
        }
        $sql = "ALTER TABLE ";
        $sql .= $row->getSubModelParentRow()->table." ";
        $sql .= "DROP {$row->field}";
        $row->getSubModelParentRow()->getModel()->getDb()->query(trim($sql));

        foreach ($this->_rows[$iId] as $k=>$i) {
            if ($row === $i) {
                unset($this->_data[$iId][$k]);
                unset($this->_rows[$iId][$k]);
                return;
            }
        }
        throw new Vps_Exception("Can't find entry");
    }
}
