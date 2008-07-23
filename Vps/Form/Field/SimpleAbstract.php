<?php
abstract class Vps_Form_Field_SimpleAbstract extends Vps_Form_Field_Abstract
{
    public function load($row)
    {
        $ret = array();
        if ($this->getSave() !== false) {
            $ret[$this->getFieldName()] = $this->getData()->load($row);
        }
        return array_merge($ret, parent::load($row));
    }

    protected function _addValidators()
    {
        parent::_addValidators();
    }

    public function validate($postData)
    {
        $ret = parent::validate($postData);

        if ($this->getSave() !== false) {

            $data = $this->_getValueFromPostData($postData);

            $name = $this->getFieldLabel();
            if (!$name) $name = $this->getName();
            if ($this->getAllowBlank() === false
                || $this->getAllowBlank() === 0
                || $this->getAllowBlank() === '0') {
                $v = new Vps_Validate_NotEmpty();
                if (!$v->isValid($data)) {
                    $ret[] = $name.": ".implode("<br />\n", $v->getMessages());
                }
            }
            if (!is_null($data)) {
                foreach ($this->getValidators() as $v) {
                    if (!$v->isValid($data)) {
                        $ret[] = $name.": ".implode("<br />\n", $v->getMessages());
                    }
                }
            }
        }
        return $ret;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        parent::prepareSave($row, $postData);

        if ($this->getSave() !== false) {
            $data = $this->_getValueFromPostData($postData);
            $this->getData()->save($row, $data);
        }
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = null;
        return $postData[$fieldName];
    }
}
