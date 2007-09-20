<?php
abstract class Vps_Auto_Field_SimpleAbstract extends Vps_Auto_Field_Abstract
{
    public function load($row)
    {
        if ((array)$row == array()) return array();

        $ret = array();

        $fieldName = $this->getFieldName();
        if ($this->getFindParent()) {
            $parentRow = $row->findParentRow($this->getFindParent());
            if (!$parentRow) throw new Vps_Exception("Can't find parent row.");
            $ret[$fieldName] = $parentRow->__toString();
        } else {
            $name = $this->getName();
            if (!isset($row->$name)) {
                throw new Vps_Exception("Index '$name' doesn't exist in row.");
            }
            $ret[$fieldName] = $row->$name;
        }
        return array_merge($ret, parent::load($row));
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        $name = $this->getName();
        $fieldName = $this->getFieldName();
        
        if (isset($postData[$fieldName])) {
            $row->$name = $postData[$fieldName];
        }
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->save($row, $postData);
            }
        }
        parent::load($row);
    }
}
