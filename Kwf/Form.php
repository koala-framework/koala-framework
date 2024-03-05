<?php
/**
 * Container for all forms
 *
 * Introduction to Forms: https://github.com/vivid-planet/koala-framework/wiki/Kwf_Form
 *
 * @package Form
 */
class Kwf_Form extends Kwf_Form_NonTableForm
{
    protected $_tableName;
    protected $_modelName;
    protected $_model;
    private $_primaryKey;
    private $_rows = array();
    private $_hideForValue = array();
    private $_createdRows = array();

    protected function _init()
    {
        parent::_init();
        if (isset($this->_tableName) && !isset($this->_model)) {
            $this->setTable(new $this->_tableName());
        }
        if (isset($this->_modelName) && !isset($this->_model)) {
            $this->_model = Kwf_Model_Abstract::getInstance($this->_modelName);
        }
        if (is_string($this->_model)) {
            $this->_model = Kwf_Model_Abstract::getInstance($this->_model);
        }
    }

    /**
     * Hide a field has a specific value, hide other fields
     *
     * ATM only implemented in Frontend Form
     */
    public function hideForValue(Kwf_Form_Field_Abstract $field, $value, Kwf_Form_Field_Abstract $hideField)
    {
        $this->_hideForValue[] = array('field' => $field, 'value' => $value, 'hide' => $hideField);
        return $this;
    }

    public function getHideForValue()
    {
        return $this->_hideForValue;
    }

    public function getMetaData($model = null)
    {
        $ret = parent::getMetaData($this->getModel());
        /*
        TODO: implement hideForValue in Ext forms, then this below is needed
        $ret['hideForValue'] = array();
        foreach ($this->_hideForValue as $v) {
            $ret['hideForValue'][] = array(
                'field' => $v['field']->getFieldName(),
                'value' => $v['value'],
                'hide' => $v['hide']->getFieldName(),
            );
        }
        */
        return $ret;
    }

    public function processInput($parentRow, $postData = array())
    {
        $ret = parent::processInput($parentRow, $postData);
        foreach ($this->_hideForValue as $v) {
            if (isset($ret[$v['field']->getFieldName()]) && $ret[$v['field']->getFieldName()] == $v) {
                $this->fields->remove($v['hide']);
            }
        }
        return $ret;
    }

    //kann überschrieben werden wenn wir eine anderen row haben wollen
    //aber besser getRow überschreiben!!!
    protected function _getRowByParentRow($parentRow)
    {
        if ($parentRow && $this->_model instanceof Kwf_Model_Field) {
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
            throw new Kwf_Exception('Can\'t find row.');
        } else if (!$row instanceof Kwf_Model_Row_Interface) {
            throw new Kwf_Exception('Row must be a Kwf_Model_Row_Interface.');
        }
        parent::prepareSave($parentRow, $postData);
    }

    public function save($parentRow, $postData)
    {
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        if (!$row) {
            throw new Kwf_Exception('Can\'t find row.');
        } else if (!$row instanceof Kwf_Model_Row_Interface) {
            throw new Kwf_Exception('Row must be a Kwf_Model_Row_Interface.');
        }

        if ($this->getIdTemplate()) {
            $field = $this->getIdTemplateField() ? $this->getIdTemplateField() : $this->getPrimaryKey();
            if (!$row->$field && $this->getIdTemplate()) {
                $row->$field = $this->_getIdByParentRow($parentRow);
            }
        }

        if (!$this->getId()) {
            $this->_beforeInsert($row);
        } else {
            $this->_beforeUpdate($row);
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
        } else {
            $this->_afterUpdate($row);
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


    public function delete(Kwf_Model_Row_Interface $parentRow)
    {
        $row = $this->_getRowByParentRow($parentRow);
        if (!$row) {
            throw new Kwf_Exception('Can\'t find row.');
        } else if (!$row instanceof Kwf_Model_Row_Interface) {
            throw new Kwf_Exception('Row must be a Kwf_Model_Row_Interface.');
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
            throw new Kwf_Exception("You have to set either the primaryKey or the model.");
        }
        return $this->_primaryKey;
    }

    public function getModel()
    {
        return $this->_model;
    }
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Damit bei verschachtelten Forms die das selben Model verwenden
     * nicht zwei unterschiedliche rows verwendet werden, was beim hinzufügen ein problem ist.
     *
     * Wird aufgerufen von getRow, in Kwc_User_Edit_Form_Form wirds auch verwendet
     */
    protected final function _rowIsParentRow($parentRow)
    {
        $id = $this->_getIdByParentRow($parentRow);
        if ($parentRow && !$parentRow->{$parentRow->getModel()->getPrimaryKey()}) {
            //remember _createdRows, because once it is saved it will have an id and we can't compare it to $id anymore
            $this->_createdRows[] = $parentRow;
        }
        if ($parentRow && !$parentRow instanceof Kwf_Model_FnF_Row
            && $parentRow->getModel()->isEqual($this->_model)
        ) {
            if ($parentRow->{$parentRow->getModel()->getPrimaryKey()} == $id) {
                return true;
            }
            if (!$id && in_array($parentRow, $this->_createdRows, true)) {
                return true;
            }
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
            throw new Kwf_Exception("_model has to be set for form '".get_class($this)."'");
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
            $s = new Kwf_Model_Select();
            $s->whereEquals($this->_model->getPrimaryKey(), $id);
            $row = $this->_model->getRow($s);
        }

        if (!$row) {
            if ($this->getCreateMissingRow()) { //für Kwf_Form_AddForm
                $this->_rows[$key] = $this->_createMissingRow($id);
            } else {
                throw new Kwf_Exception("No database-entry found for id '$id' in model ".get_class($this->_model));
            }
        } else {
            $this->_rows[$key] = $row;
        }

        return $this->_rows[$key];
    }

    protected function _createMissingRow($id)
    {
        $ret = $this->_model->createRow();
        $ret->{$this->getPrimaryKey()} = $id;
        return $ret;
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeUpdate(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterUpdate(Kwf_Model_Row_Interface $row)
    {
    }

    /**
     * Static helper function that formats form errors
     *
     * @return string[]
     */
    public static function formatValidationErrors($errors)
    {
        $msg = array();
        foreach ($errors as $i) {
            if (!is_array($i)) {
                throw new Kwf_Exception('Form errors must be of type array');
            }
            $name = '';
            if (isset($i['field'])) {
                $name = $i['field']->getFieldLabel();
                if (!$name) $name = $i['field']->getName();
            }
            if (isset($i['message'])) {
                $i['messages'] = array($i['message']);
            }
            foreach ($i['messages'] as $m) {
                $msg[] = ($name ? ($name.': ') : '').Kwf_Util_HtmlSpecialChars::filter($m);
            }
        }
        return $msg;
    }
}
