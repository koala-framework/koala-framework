<?php
class Vps_Auto_Form extends Vps_Auto_Container_Abstract
{
    private $_name;
    private $_id;
    private $_table;
    private $_primaryKey;
    protected $_row;

    public function __construct($name = null, $id = null)
    {
        $this->fields = new Vps_Collection_FormFields();
        $this->setName($name);
        $this->setId($id);
        $this->setLayout('form');
        $this->setBorder(false);
    }

    public function prepareSave($parentRow, $postData)
    {
        $row = $this->getRow();
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Zend_Db_Table_Row_Abstract) {
            throw new Vps_Exception('Row must be a Zend_Db_Table_Row_Abstract.');
        }
        parent::prepareSave($row, $postData);
    }

    public function save($parentRow, $postData)
    {
        $row = $this->getRow();
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Zend_Db_Table_Row_Abstract) {
            throw new Vps_Exception('Row must be a Zend_Db_Table_Row_Abstract.');
        }

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

    public function load($parentRow)
    {
        $row = (object)$this->getRow();
        return parent::load($row);
    }

    public function delete($parentRow)
    {
        $row = $this->getRow();
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Zend_Db_Table_Row_Abstract) {
            throw new Vps_Exception('Row must be a Zend_Db_Table_Row_Abstract.');
        }
        parent::delete($row);
        $row->delete();
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
        $this->fields->setFormName($name); //damit prefixName der Felder nachträglich angepasst wird
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
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
            throw new Vps_Exception('Either _table has to be set or _fetchData has to be overwritten.');
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
                throw new Vps_ClientException('No database-entry found.');
            } else if ($rowset->count() > 1) {
                throw new Vps_ClientException('More than one database-entry found.');
            } else {
                $this->_row = $rowset->current();
            }
        }
        return $this->_row;
    }
}
