<?php
class Vps_Auto_Form implements Vps_Collection_Item_Interface
{
    public $fields = null;
    private $_name;
    private $_id;
    private $_table;
    private $_primaryKey;

    private $_row;
    private $_properties = array();

    public function __construct($name = null, $id = null)
    {
        $this->fields = new Vps_Collection_FormFields();
        $this->setName($name);
        $this->setId($id);
    }
    
    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->setProperty($name, $arguments[0]);
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }
    
    public function getProperties()
    {
        return $this->_properties;
    }

    public function prepareSave($parentRow, $postData)
    {
        $row = (object)$this->getRow();
        if(!$row) {
            throw new Vps_Exception('Can\'t find row.');
        }

        foreach($this->fields as $field) {
            $field->save($row, $postData);
        }
        $this->_beforeSave($row);
        $primaryKey = $this->getPrimaryKey();

        //zum überprüfen ob es ein neuer eintrag ist reicht wenn wir einfach den ersten nehmen
        //todo: verbessern: die row sollte das womöglich selbst wissen
        if (is_array($primaryKey)) $primaryKey = $primaryKey[1];

        if (!$row->$primaryKey) {
            $this->_beforeInsert($row);
        }
    }

    public function save($parentRow)
    {
        $row = (object)$this->getRow();

        $row->save();

        $this->_afterSave($row);
        $primaryKey = $this->getPrimaryKey();
        if (is_array($primaryKey)) $primaryKey = $primaryKey[1];
        if (!$row->$primaryKey) {
            $this->_afterInsert($row);
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

    public function load()
    {
        $ret = array();
        $row = (object)$this->getRow();
        foreach($this->fields as $field) {
            $ret = array_merge($ret, $field->load($row));
        }
        return $ret;
    }

    public function delete($parentRow)
    {
        $ret = array();
        $row = (object)$this->_fetchData();
        foreach($this->fields as $field) {
            $field->delete($row);
        }
        return $ret;
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

    public function getByName($name)
    {
        if ($this->getName() == $name) {
            return $this;
        } else {
            return $this->fields->getByName($name);
        }
    }

    public function getPrimaryKey()
    {
        if (!isset($this->_primaryKey) && isset($this->_table)) {
            if(!isset($this->_primaryKey)) {
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

    public function hasChildren()
    {
        return sizeof($this->fields) > 0;
    }
    public function getChildren()
    {
        return $this->fields;
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

    public function getMetaData()
    {
        $ret = array();
        foreach ($this->fields as $field) {
            $data = $field->getMetaData();
            if ($data) {
                if ($field instanceof Vps_Auto_Form) { //TODO: better solution here
                    $ret = array_merge($ret, $data);
                } else {
                    $ret[] = $data;
                }
            }
        }
        return $ret;
    }
    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterInsert(Zend_Db_Table_Row_Abstract $row)
    {
    }

    public function add($v = null)
    {
        return $this->fields->add($v);
    }
}
