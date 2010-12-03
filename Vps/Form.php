<?php
class Vps_Form extends Vps_Form_NonTableForm
{
    protected $_tableName;
    protected $_modelName;
    protected $_model;
    private $_primaryKey;
    private $_rows = array();

    protected function _init()
    {
        parent::_init();
        if (isset($this->_tableName) && !isset($this->_model)) {
            $this->setTable(new $this->_tableName());
        }
        if (isset($this->_modelName) && !isset($this->_model)) {
            $this->_model = Vps_Model_Abstract::getInstance($this->_modelName);
        }
    }

    public function getMetaData()
    {
        return parent::getMetaData($this->getModel());
    }

    //kann überschrieben werden wenn wir eine anderen row haben wollen
    //aber besser getRow überschreiben!!!
    protected function _getRowByParentRow($parentRow)
    {
        if ($parentRow && $this->_model instanceof Vps_Model_Field) {
            $ret = $this->_model->getRowByParentRow($parentRow);
        } else {
            $ret = $this->getRow($parentRow);
        }
        if (is_null($ret)) return $ret;
        return (object)$ret;
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
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        if (!$row) {
            throw new Vps_Exception('Can\'t find row.');
        } else if (!$row instanceof Vps_Model_Row_Interface) {
            throw new Vps_Exception('Row must be a Vps_Model_Row_Interface.');
        }

        if ($this->getIdTemplate()) {
            $field = $this->getIdTemplateField() ? $this->getIdTemplateField() : $this->getPrimaryKey();
            if (!$row->$field && $this->getIdTemplate()) {
                $row->$field = $this->_getIdByParentRow($parentRow);
            }
        }

        if (!$this->getId()) {
            $this->_beforeInsert($row);
        }
        $this->_beforeSave($row);

        if (!$this->_rowIsParentRow($parentRow)) {
            //speichern *vor* parent::save wegen:
            //- verschachtelten forms, beim hinzufügen brauchen die kinder die neue id
            //- MultiFields auch beim hinzufügen ohne Model Relation brauchen wir die neue id
            $row->save();
        }
        parent::save($parentRow, $postData);

        if (!$this->getId()) {
            $this->_afterInsert($row);
        }
        $this->_afterSave($row);

        if (!$this->getId()) {
            $primaryKey = $this->getPrimaryKey();
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
        return $this;
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

    /**
     * Damit bei verschachtelten Forms die das selben Model verwenden
     * nicht zwei unterschiedliche rows verwendet werden, was beim hinzufügen ein problem ist.
     *
     * Wird aufgerufen von getRow, in Vpc_User_Edit_Form_Form wirds auch verwendet
     */
    protected final function _rowIsParentRow($parentRow)
    {
        $id = $this->_getIdByParentRow($parentRow);
        if ($parentRow && !$parentRow instanceof Vps_Model_FnF_Row
            && $parentRow->getModel()->isEqual($this->_model)
            && $parentRow->{$parentRow->getModel()->getPrimaryKey()} == $id
        ) {
            return true;
        }
        return false;
    }

    public function getRow($parentRow = null)
    {
        if (!$parentRow && $this->getIdTemplate()) {
            //tritt auf in Cards bei einer nicht aktiven card (da ist parentRow null)
            return null;
        }

        $key = 'none';
        if ($parentRow) {
            $key = $parentRow->getInternalId();
        }
        if (isset($this->_rows[$key])) return $this->_rows[$key];

        if (!isset($this->_model)) {
            throw new Vps_Exception("_model has to be set for form '".get_class($this)."'");
        }
        $rowset = null;

        if ($this->_rowIsParentRow($parentRow)) {
            return $parentRow;
        }

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
                if ($this->getCreateMissingRow()) { //für Vps_Form_AddForm
                    $this->_rows[$key] = $this->_model->createRow();
                    $this->_rows[$key]->{$this->getPrimaryKey()} = $id;
                } else {
                    throw new Vps_Exception('No database-entry found.');
                }
            } else if (count($rowset) > 1) {
                throw new Vps_Exception('More than one database-entry found.');
            } else {
                $this->_rows[$key] = $rowset->current();
            }
        }
        return $this->_rows[$key];
    }


    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
    }

    protected function _afterInsert(Vps_Model_Row_Interface $row)
    {
    }

    public static function formatValidationErrors($errors)
    {
        $msg = array();
        foreach ($errors as $i) {
            $name = '';
            if (isset($i['field'])) {
                $name = $i['field']->getFieldLabel();
                if (!$name) $name = $i['field']->getName();
            }
            if (isset($i['message'])) {
                $i['messages'] = array($i['message']);
            }
            foreach ($i['messages'] as $m) {
                $msg[] = ($name ? ($name.': ') : '').$m;
            }
        }
        return $msg;
    }
}
