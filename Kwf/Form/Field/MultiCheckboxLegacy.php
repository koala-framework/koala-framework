<?php
/**
 * @package Form
 * @internal
 */
//todo: validators
class Kwf_Form_Field_MultiCheckboxLegacy extends Kwf_Form_Field_Abstract
{
    protected $_fields;
    private $_references;
    private $_model;

    //Einstellungen:
    //setColumnName('id')
       //- wenn bei setValues() kein rowset übergeben wird
    //setReferences
       //- wenn ned über innoDb ermittelt werden können

    public function __construct($tableName = null, $title = null)
    {
        parent::__construct();

        if (is_object($tableName)) {
            $model = $tableName;
        } else if (class_exists($tableName)) {
            $model = new $tableName();
        } else {
            throw new Kwf_Exception("'$tableName' does not exist");
        }

        parent::__construct(get_class($model));
        $this->setModel($model);

        if ($title) $this->setTitle($title);
        $this->setHideLabels(true);
        $this->setAutoHeight(true);
        $this->setLayout('form');
        $this->setXtype('fieldset');
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        $ret['items'] = $this->_getFields()->getMetaData($model);
        if (!$ret['items']) unset($ret['items']);
        if (isset($ret['tableName'])) unset($ret['tableName']);
        if (isset($ret['values'])) unset($ret['values']);
        return $ret;
    }

    protected function _getFields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = new Kwf_Collection_FormFields();
            if ($this->getValues() instanceof Kwf_Model_Rowset_Interface) {
                $pk = $this->getValues()->getModel()->getPrimaryKey();
            }
            foreach ($this->getValues() as $key => $i) {
                if (isset($pk)) {
                    $key = $i->$pk;
                }
                if (!is_string($i)) $i = $i->__toString();
                $this->_fields->add(new Kwf_Form_Field_Checkbox($this->getFieldName()."[$key]"))
                    ->setKey($key)
                    ->setBoxLabel($i);
            }
        }
        return $this->_fields;
    }

    public function hasChildren()
    {
        return sizeof($this->_fields) > 0;
    }
    public function getChildren()
    {
        return $this->_fields;
    }

    public function getName()
    {
        $name = parent::getName();
        if (!$name) {
            $name = $this->getTableName();
        }
        return $name;
    }
    protected function _getRowsByRow(Kwf_Model_Row_Interface $row)
    {
        if ($this->_model instanceof Kwf_Model_FieldRows) {
            $rows = $this->_model->fetchByParentRow($row);
        } else {
            $pk = $row->getModel()->getPrimaryKey();
            if (!$row->$pk) {
                //neuer eintrag (noch keine id)
                return array();
            }
            $ref = $this->_getReferences($row);
            $select = $this->_model->select();
            foreach (array_keys($ref['columns']) as $k) {
                $select->whereEquals($ref['columns'][$k],
                        $row->{$ref['refColumns'][$k]});
            }
            $rows = $this->_model->getRows($select);
        }
        return $rows;
    }
    protected function _getReferences($row)
    {
        if ($this->_references) {
            return $this->_references;
        } else if ($this->_model instanceof Kwf_Model_Db && $row instanceof Kwf_Model_Db_Row) {
            return $this->_model->getTable()
                        ->getReference(get_class($row->getRow()->getTable()));
        } else {
            throw new Kwf_Exception('Couldn\'t read references for Multifields. Either use Kwf_Model_FieldRows/Kwf_Model_Db or set the References by setReferences().');
        }
    }
    public function setReferences($references)
    {
        $this->_references = $references;
        return $this;
    }

    public function load($row, $postData = array())
    {
        if (!$row) return array();

        $selected = $this->_getRowsByRow($row);
        $key = $this->getColumnName();

        $selectedIds = array();
        foreach ($selected as $i) {
            $selectedIds[] = $i->$key;
        }

        $ret = array();
        foreach ($this->_getFields() as $field) {
            $ret[$field->getFieldName()] = in_array($field->getKey(), $selectedIds);
        }

        return $ret;
    }
    public function getColumnName()
    {
        $ret = $this->getProperty('columnName');
        if (!$ret) {
            if (get_class($this->_model) == 'Kwf_Model_Db') {
                if ($this->getValues()->getModel()  instanceof Kwf_Util_Model_Pool) {
                    $tableClass = 'Kwf_Dao_Pool';
                } else {
                    $tableClass = get_class($this->getValues()->getTable());
                }
                $ref = $this->_model->getTable()->getReference($tableClass);
                $ret = $ref['columns'][0];
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
        }
        return $ret;
    }
    public function save($row, $postData)
    {
        $new = array();
        if (isset($postData[$this->getFieldName()]) && $postData[$this->getFieldName()]) {
            foreach ($postData[$this->getFieldName()] as $key=>$value) {
                if ($value) $new[] = $key;
            }
        }
        if ($this->getAllowBlank() === false && $new == array()) {
            throw new Kwf_ClientException("Please select at least one ".$this->getTitle().".");
        }
        $saved = $this->_getRowsByRow($row);

        $ref = $this->_getReferences($row);
        $key1 = $ref['columns'][0];
        
        $key2 = $this->getColumnName();

        $avaliableKeys = array();
        foreach ($this->_getFields() as $field) {
            $avaliableKeys[] = $field->getKey();
        }

        foreach ($saved as $savedRow) {
            $id = $savedRow->$key2;
            if (in_array($id, $avaliableKeys)) {
                if (!in_array($id, $new)) {
                    $savedRow->delete();
                    continue;
                } else {
                    unset($new[array_search($id, $new)]);
                }
            }
        }

        $ref = $this->_getReferences($row);
        foreach ($new as $id) {
            if (in_array($id, $avaliableKeys)) {
                $i = $this->_model->createRow();
                $i->$key1 = $row->{$ref['refColumns'][0]};
                $i->$key2 = $id;
                $i->save();
            }
        }
    }
}
