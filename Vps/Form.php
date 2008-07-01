<?php
class Vps_Form extends Vps_Form_NonTableForm
{
    protected $_tableName;
    protected $_modelName;
    protected $_model;
    private $_primaryKey;
    private $_rows = array();

    public function __construct($name = null)
    {
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        parent::__construct($name);
    }

    protected function _init()
    {
        parent::_init();
        if (isset($this->_tableName) && !isset($this->_model)) {
            $this->setTable(new $this->_tableName());
        }
        if (isset($this->_modelName) && !isset($this->_model)) {
            $this->_model = new $this->_modelName();
        }
    }

    //kann überschrieben werden wenn wir eine anderen row haben wollen
    protected function _getRowByParentRow($parentRow)
    {
        if ($parentRow && $this->_model instanceof Vps_Model_Field) {
            return $this->_model->getRowByParentRow($parentRow);
        } else {
            return $this->getRow($parentRow);
        }
    }


    public function prepareSave($parentRow, $postData)
    {
        $row = $this->_getRowByParentRow($parentRow);
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Vps_Model_Row_Interface) {
            throw new Vps_Exception('Row must be a Vps_Model_Row_Interface.');
        }
        parent::prepareSave($parentRow, $postData);
    }

    public function save($parentRow, $postData)
    {
        //wenn form zB in einem CardLayout liegt und deaktivert wurde nicht speichern
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Vps_Model_Row_Interface) {
            throw new Vps_Exception('Row must be a Vps_Model_Row_Interface.');
        }

        $primaryKey = $this->getPrimaryKey();
        if (!$row->$primaryKey && $this->getIdTemplate()) {
            $row->$primaryKey = $this->_getIdByParentRow($parentRow);
        }

        $row->save();
        parent::save($parentRow, $postData);

        if (!$this->getId()) {
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
        $row = $this->_getRowByParentRow($parentRow);
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Vps_Model_Row_Interface) {
            throw new Vps_Exception('Row must be a Vps_Model_Row_Interface.');
        }
        parent::delete($parentRow);
        $row->delete();
    }

    public function getPrimaryKey()
    {
        if (!isset($this->_primaryKey) && isset($this->_model)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
        }
        if (!isset($this->_primaryKey)) {
            throw new Vps_Exception("You have to set either the primaryKey or the model.");
        }
        return $this->_primaryKey;
    }


    public function setTable(Zend_Db_Table_Abstract $table)
    {
        $this->_model = new Vps_Model_Db(array(
            'table' => $table
        ));
    }
    public function getModel()
    {
        return $this->_model;
    }
    public function setModel(Vps_Model_Interface $model)
    {
        $this->_model = $model;
        return $this;
    }

    public function getRow($parentRow = null)
    {
        $key = 'none';
        if ($parentRow) {
            $key = $parentRow->getInternalId();
        }
        if (isset($this->_rows[$key])) return $this->_rows[$key];

        if (!isset($this->_model)) {
            throw new Vps_Exception('_model has to be set');
        }
        $rowset = null;

        $id = $this->_getIdByParentRow($parentRow);

        if (is_array($this->getPrimaryKey())) {
            $where = array();
            foreach ($this->getPrimaryKey() as $key) {
                if ($id[$key]) {
                    $where[$key . ' = ?'] = $id[$key];
                }
            }
            if (!empty($where)) {
                $rowset = $this->_model->fetchAll($where);
            }
        } else if ($id === 0 || $id === '0' || is_null($id)) {
            $this->_rows[$key] = $this->_model->createRow();
            return $this->_rows[$key];
        } else if ($id) {
            $rowset = $this->_model->find($id);
        }

        if (!$rowset) {
            return null;
        } else {
            if (count($rowset)== 0) {
                throw new Vps_Exception('No database-entry found.');
            } else if (count($rowset) > 1) {
                throw new Vps_Exception('More than one database-entry found.');
            } else {
                $this->_rows[$key] = $rowset->current();
            }
        }
        return $this->_rows[$key];
    }
}
