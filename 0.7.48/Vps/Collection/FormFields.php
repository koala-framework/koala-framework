<?php
class Vps_Collection_FormFields extends Vps_Collection
{
    private $_formName = null;

    public function __construct($name = null)
    {
        $this->_formName = $name;
    }

    protected function _postInsertValue($field)
    {
        if ($this->_formName) {
            $field->setNamePrefix($this->_formName);
        }
    }

    public function setFormName($name)
    {
        $this->_formName = $name;
        foreach ($this as $field) {
            $this->_postInsertValue($field);
        }
    }

    public function getFormName()
    {
        return $this->_formName;
    }
}
