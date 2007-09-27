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
            if ($this->getFindParentField()) {
                $f = $this->getFindParentField();
                $ret[$fieldName] = $parentRow->$f;
            } else {
                $ret[$fieldName] = $parentRow->__toString();
            }
        } else {
            $name = $this->getName();
            if (!isset($row->$name)) {
                throw new Vps_Exception("Index '$name' doesn't exist in row.");
            }
            $ret[$fieldName] = $row->$name;
        }
        return array_merge($ret, parent::load($row));
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        if ($this->getAllowBlank() === false) {
            $this->addValidator(new Zend_Validate_NotEmpty());
        }
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        parent::prepareSave($row, $postData);

        if ($this->getSave() !== false) {
            $name = $this->getName();
            $fieldName = $this->getFieldName();
            
            if (isset($postData[$fieldName])) {
                $data = $postData[$fieldName];
                foreach($this->getValidators() as $v) {
                    if (!$v->isValid($data)) {
                        throw new Vps_ClientException(implode("<br />\n", $v->getMessages()));
                    }
                }
                $row->$name = $data;
            }
        }
    }
}
