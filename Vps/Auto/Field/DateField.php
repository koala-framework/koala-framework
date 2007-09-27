<?php
class Vps_Auto_Field_DateField extends Vps_Auto_Field_SimpleAbstract
{
    protected $_xtype = 'datefield';

    protected function _addValidators()
    {
        if ($this->getAllowBlank() === false) {
            $this->addValidator(new Zend_Validate_Date());
        }
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        Vps_Auto_Field_Abstract::prepareSave($row, $postData);

        $name = $this->getName();
        $fieldName = $this->getFieldName();

        if (isset($postData[$fieldName])) {
            if ($postData[$fieldName]) {
                $row->$name = $postData[$fieldName];
            } else {
                $row->$name = null;
            }
        }
    }
}
