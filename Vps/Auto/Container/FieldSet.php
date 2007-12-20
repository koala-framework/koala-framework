<?php
class Vps_Auto_Container_FieldSet extends Vps_Auto_Container_Abstract
{
    private $_checkboxHiddenField = null;
    public function __construct($title = null)
    {
        parent::__construct();
        $this->setTitle($title);
        $this->setAutoHeight(true);
        $this->setXtype('fieldset');
    }

    public function setCheckboxName($name)
    {
        $this->_checkboxHiddenField = new Vps_Auto_Field_Hidden($name);
        $this->fields->add($this->_checkboxHiddenField);
        return $this->setProperty('checkboxName', $name);
    }

    public function prepareSave($row, $postData)
    {
        if ($this->getCheckboxName()) {
            if (!isset($postData[$this->getCheckboxName()])
                || !$postData[$this->getCheckboxName()]) {
                foreach ($this->fields as $f) {
                    if ($f != $this->_checkboxHiddenField) {
                        $f->setSave(false);
                    }
                }
            }
        }
        parent::prepareSave($row, $postData);
    }
}
