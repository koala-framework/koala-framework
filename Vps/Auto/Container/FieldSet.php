<?php
class Vps_Auto_Container_FieldSet extends Vps_Auto_Container_Abstract
{
    private $_checkboxHiddenField = null;
    public function __construct($title = null)
    {
        parent::__construct();
        $this->setTitle($title);
        $this->setAutoHeight(true);
        $this->setBorder(true);
        $this->setXtype('fieldset');
    }

    public function setCheckboxName($name)
    {
        $this->_checkboxHiddenField = new Vps_Auto_Field_Hidden($name);
        $this->fields->add($this->_checkboxHiddenField);
        return $this;
    }

    public function validate($postData)
    {
        if ($this->_checkboxHiddenField) {
            $n = $this->_checkboxHiddenField->getFieldName();
            if (!isset($postData[$n]) || !$postData[$n]) {
                foreach ($this->fields as $f) {
                    if ($f != $this->_checkboxHiddenField) {
                        $f->setSave(false);
                    }
                }
            }
        }
        return parent::validate($postData);
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if ($this->_checkboxHiddenField) {
            $ret['checkboxName'] = $this->_checkboxHiddenField->getFieldName();
        }
        return $ret;
    }
}
