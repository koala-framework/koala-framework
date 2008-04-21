<?php
class Vps_Auto_Form extends Vps_Auto_NonTableForm
{
    protected $_table;
    protected $_tableName;
    private $_primaryKey;
    protected $_row;

    public function __construct($name = null, $id = null)
    {
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        parent::__construct($name);
        $this->setId($id);
    }

    protected function _init()
    {
        parent::_init();
        if (isset($this->_tableName) && !isset($this->_table)) {
            $this->_table = new $this->_tableName();
        }
    }

    //kann überschrieben werden wenn wir eine anderen row haben wollen
    protected function _getRowByParentRow($parentRow)
    {
        return $this->getRow();
    }

    public function save($parentRow, $postData)
    {
        //wenn form zB in einem CardLayout liegt und deaktivert wurde nicht speichern
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        $row->save();
        parent::save($row, $postData);

        $primaryKey = $this->getPrimaryKey();
        if (is_array($primaryKey)) $primaryKey = $primaryKey[1];
        if ($this->getId() == 0) {
            if (is_array($primaryKey)) {
                $addedId = array();
                foreach ($primaryKey as $key) {
                    $addedId[$key] = $row->$key;
                }
            } else {
                $addedId = $row->$primaryKey;
            }
            return array('addedId' => $addedId);
        }
        return array();
    }


    public function delete($parentRow)
    {
        parent::delete($parentRow);

        $row = $this->_getRowByParentRow($parentRow);
        $row->delete();
    }

    public function getPrimaryKey()
    {
        if (!isset($this->_primaryKey) && isset($this->_table)) {
            if (!isset($this->_primaryKey)) {
                $info = $this->_table->info();
                $this->_primaryKey = $info['primary'];
                if (sizeof($this->_primaryKey) == 1) {
                    $this->_primaryKey = $this->_primaryKey[1];
                }
            }
        }
        if (!isset($this->_primaryKey)) {
            throw new Vps_Exception("You have to set either the primaryKey or the table.");
        }
        return $this->_primaryKey;
    }


    public function setTable(Zend_Db_Table_Abstract $table)
    {
        $this->_table = $table;
    }
    public function getTable()
    {
        return $this->_table;
    }

    public function getRow()
    {
        if (isset($this->_row)) return $this->_row;

        if (!isset($this->_table)) {
            throw new Vps_Exception('_table has to be set');
        }
        $rowset = null;

        $id = $this->getId();
        if (is_array($this->getPrimaryKey())) {
            $where = array();
            foreach ($this->getPrimaryKey() as $key) {
                if ($id[$key]) {
                    $where[$key . ' = ?'] = $id[$key];
                }
            }
            if (!empty($where)) {
                $rowset = $this->_table->fetchAll($where);
            }
        } else if ($id == 0) {
            $this->_row = $this->_table->createRow();
            return $this->_row;
        } else if ($id) {
            $rowset = $this->_table->find($id);
        }
        if (!$rowset) {
            return null;
        } else {
            if ($rowset->count() == 0) {
                throw new Vps_Exception('No database-entry found.');
            } else if ($rowset->count() > 1) {
                throw new Vps_Exception('More than one database-entry found.');
            } else {
                $this->_row = $rowset->current();
            }
        }
        return $this->_row;
    }
}
