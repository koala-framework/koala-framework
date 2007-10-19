<?php
//todo: validators
class Vps_Auto_Field_MultiCheckbox extends Vps_Auto_Field_Abstract
{
    protected $_fields;

    public function __construct($tableName = null, $title = null)
    {
        parent::__construct();
        $this->setTableName($tableName);
        if ($title) $this->setTitle($title);
        $this->setHideLabels(true);
        $this->setAutoHeight(true);
        $this->setLayout('form');
        $this->setBaseCls('x-plain');
        $this->setXtype('fieldset');
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['items'] = $this->_getFields()->getMetaData();
        if (isset($ret['tableName'])) unset($ret['tableName']);
        if (isset($ret['values'])) unset($ret['values']);
        return $ret;
    }

    protected function _getFields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = new Vps_Collection();
            $info = $this->getValues()->getTable()->info();
            $pk = $info['primary'][1];
            foreach ($this->getValues() as $i) {
                $k = $i->$pk;
                if (!is_string($i)) $i = $i->__toString();
                $this->_fields->add(new Vps_Auto_Field_Checkbox($this->getName()."[$k]"))
                    ->setKey($k)
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
            $name = strtolower($this->getTableName());
        }
        return $name;
    }

    public function load($row)
    {
        if ((array)$row == array()) return array();

        $selected = $row->findDependentRowset($this->getTableName());
        $ref = $selected->getTable()->getReference(get_class($this->getValues()->getTable()));
        $key = $ref['columns'][0];

        $selectedIds = array();
        foreach ($selected as $i) {
            $selectedIds[] = $i->$key;
        }

        foreach ($this->_getFields() as $field) {
            $ret[$field->getFieldName()] = in_array($field->getKey(), $selectedIds);
        }

        return $ret;
    }
    public function save(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        $new = array();
        if ($postData[$this->getFieldName()]) {
            foreach($postData[$this->getFieldName()] as $key=>$value) {
                if ($value) $new[] = $key;
            }
        }
        $saved = $row->findDependentRowset($this->getTableName());

        $ref = $saved->getTable()->getReference(get_class($row->getTable()));
        $key1 = $ref['columns'][0];
        
        $ref = $saved->getTable()->getReference(get_class($this->getValues()->getTable()));
        $key2 = $ref['columns'][0];

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

        $tableName = $this->getTableName();
        $table = new $tableName();
        foreach($new as $id) {
            if (in_array($id, $avaliableKeys)) {
                $i = $table->createRow();
                $i->$key1 = $row->id;
                $i->$key2 = $id;
                $i->save();
            }
        }
    }

    public function delete(Zend_Db_Table_Row_Abstract $row)
    {
        //todo
        //ist das nicht aufgabe des models oder der datenbank?
    }
}
