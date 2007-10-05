<?php
abstract class Vps_Auto_Field_SimpleAbstract extends Vps_Auto_Field_Abstract
{
    public function load($row)
    {
        $ret = array();
        $ret[$this->getFieldName()] = $this->getData()->load($row);
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
            $fieldName = $this->getFieldName();

            if (isset($postData[$fieldName])) {
                $data = $postData[$fieldName];
                foreach($this->getValidators() as $v) {
                    if (!$v->isValid($data)) {
                        if ($this->getFieldLabel()) $name = $this->getFieldLabel();
                        if ($this->getFieldLabel()) $name = $this->getName();
                        throw new Vps_ClientException($name.": ".implode("<br />\n", $v->getMessages()));
                    }
                }
                $this->getData()->save($row, $data);
            }
        }
    }
}
